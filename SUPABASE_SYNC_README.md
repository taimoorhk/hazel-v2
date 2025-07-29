# Supabase User Sync System

This document explains how to sync users between Supabase Auth and your Laravel database.

## Overview

The system automatically creates users in the Laravel database when they sign up through Supabase Auth (Magic Links). However, you may need to manually sync existing users.

## Automatic Sync

### For New Users
- When users sign up with Magic Links, they are automatically created in the Laravel database
- The sync happens during the Magic Link confirmation process
- Users are assigned default roles and metadata

### For Existing Users
- Users are synced when they log in and visit the dashboard
- The "Sync User" button on the dashboard can manually trigger sync

## Improved Authentication Flow (NEW)

The authentication system now handles users who exist in Laravel but don't have Supabase IDs:

### How it Works
1. **User Login**: When a user enters their email on the login page, the system checks if they exist in Laravel
2. **Verification**: If the user exists in Laravel but has no Supabase ID, they are allowed to proceed with authentication
3. **Magic Link**: A magic link is sent with `shouldCreateUser: true` to create the Supabase user
4. **Confirmation**: When the user clicks the magic link, a Supabase user is created and synced back to Laravel
5. **Completion**: The user is now authenticated and has a Supabase ID

### Benefits
- ✅ Pre-registered users can now authenticate without manual intervention
- ✅ Seamless migration from Laravel-only users to dual-system users
- ✅ No data loss or manual sync required
- ✅ Maintains existing user roles and metadata

### Error Handling
- Users who don't exist in Laravel are redirected to registration
- Users with existing Supabase IDs follow the normal authentication flow
- Failed syncs are logged but don't prevent authentication

## Real-time Sync System (NEW)

The system now includes comprehensive real-time synchronization between Laravel and Supabase:

### How Real-time Sync Works
1. **Automatic Event Triggers**: Laravel automatically fires events when users are created, updated, or deleted
2. **Immediate Sync**: The RealtimeSyncService immediately syncs changes to both Supabase Auth and public.users
3. **Bidirectional Sync**: Changes in Supabase are also synced back to Laravel via real-time listeners
4. **Database Triggers**: Supabase triggers automatically maintain consistency between auth.users and public.users

### Real-time Sync Components
- **RealtimeSyncService**: Handles immediate sync from Laravel to Supabase
- **SupabaseRealtimeListener**: Listens for changes in Supabase and syncs to Laravel
- **Laravel Events**: UserCreated, UserUpdated, UserDeleted events trigger automatic sync
- **Database Triggers**: Supabase triggers maintain consistency between tables

### Real-time Sync Flow
```
Laravel User Action → Laravel Event → RealtimeSyncService → Supabase Auth + public.users
                                                                    ↓
Supabase User Action → Database Trigger → public.users → SupabaseRealtimeListener → Laravel
```

### Benefits of Real-time Sync
- ✅ **Instant Synchronization**: Changes are synced immediately
- ✅ **Bidirectional**: Both Laravel and Supabase stay in sync
- ✅ **Automatic**: No manual intervention required
- ✅ **Reliable**: Error handling and logging for failed syncs
- ✅ **Consistent**: Database triggers ensure data consistency

## Manual Sync Commands

### 1. List Users Missing Supabase IDs
```bash
php artisan supabase:list-missing-ids
```

### 2. Create a New User
```bash
php artisan make:user {email} {name} {role} {supabase_id}
```
Example:
```bash
php artisan make:user "user@example.com" "John Doe" "Normal User" "supabase-user-id"
```

### 3. Add Supabase ID to Existing User
```bash
php artisan supabase:add-id {email} {supabase_id}
```

### 4. Sync Specific User
```bash
php artisan supabase:sync-user {email}
```

### 5. Sync Existing Users with Supabase
This command creates Supabase users for existing Laravel users who don't have Supabase IDs:
```bash
# Sync all users without Supabase IDs
php artisan supabase:sync-existing-users

# Sync specific user
php artisan supabase:sync-existing-users --email=user@example.com
```

### 6. Comprehensive Sync (NEW)
This command provides bidirectional sync between Laravel and Supabase (both auth.users and public.users):
```bash
# Bidirectional sync (Laravel ↔ Supabase)
php artisan supabase:comprehensive-sync

# Sync Laravel to Supabase only
php artisan supabase:comprehensive-sync --direction=laravel-to-supabase

# Sync Supabase to Laravel only
php artisan supabase:comprehensive-sync --direction=supabase-to-laravel

# Sync specific user
php artisan supabase:comprehensive-sync --email=user@example.com
```

### 7. Real-time Sync (NEW)
This command triggers immediate real-time sync between Laravel and Supabase:
```bash
# Sync specific user
php artisan supabase:realtime-sync --email=user@example.com

# Sync all users
php artisan supabase:realtime-sync --all
```

### 8. Dispatch Background Sync Job
```bash
php artisan supabase:dispatch-sync
```

## Getting Supabase User IDs

### Method 1: Using the Helper Script
1. Get your service role key from Supabase Dashboard > Settings > API
2. Update `scripts/get-supabase-users.php` with your service role key
3. Run: `php scripts/get-supabase-users.php`

### Method 2: From Supabase Dashboard
1. Go to your Supabase Dashboard
2. Navigate to Authentication > Users
3. Copy the user IDs from the list

## API Endpoints

### Create User from Supabase
```http
POST /api/create-user-from-supabase
Content-Type: application/json

{
    "email": "user@example.com",
    "supabase_id": "user-id",
    "name": "User Name",
    "role": "Normal User",
    "user_questions": null
}
```

### Manual Sync User
```http
POST /api/manual-sync-user
Content-Type: application/json

{
    "email": "user@example.com"
}
```

### Sync User to Supabase (NEW)
```http
POST /api/sync-user-to-supabase
Content-Type: application/json

{
    "email": "user@example.com"
}
```

### Sync All Users to Supabase (NEW)
```http
POST /api/sync-all-users-to-supabase
```

### Sync All Users from Supabase (NEW)
```http
POST /api/sync-all-users-from-supabase
```

### Get Sync Status (NEW)
```http
GET /api/sync-status
```

Response:
```json
{
    "total_laravel_users": 10,
    "users_with_supabase_id": 8,
    "users_without_supabase_id": 2,
    "sync_percentage": 80.0,
    "users_needing_sync": [
        {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com"
        }
    ]
}
```

### Real-time Sync User (NEW)
```http
POST /api/realtime-sync-user
Content-Type: application/json

{
    "email": "user@example.com"
}
```

### Real-time Sync All Users (NEW)
```http
POST /api/realtime-sync-all
```

## Supabase Database Structure

The system now syncs users to both Supabase Auth (`auth.users`) and a custom `public.users` table:

### Auth Users Table (`auth.users`)
- Managed by Supabase Auth
- Contains authentication data
- Automatically synced via triggers

### Public Users Table (`public.users`)
- Mirrors Laravel users table structure
- Contains application-specific user data
- Automatically synced via triggers when auth.users changes

### Database Triggers
The system includes automatic triggers that:
1. **Create** users in `public.users` when new users are added to `auth.users`
2. **Update** users in `public.users` when `auth.users` records are modified
3. **Maintain** data consistency between both tables

### Row Level Security (RLS)
- Users can only view/update their own data
- Service role has full access for sync operations

## Configuration

### Environment Variables
Add these to your `.env` file:
```env
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
```

### Service Configuration
The Supabase configuration is already set up in `config/services.php`.

### Database Migration
Run the Supabase migration to create the users table and triggers:
```bash
# Apply the migration in your Supabase project
# The migration file is located at: supabase/migrations/20250725183410_add_onboarding_question_to_user_metadata.sql
```

## Troubleshooting

### User Not Found in Dashboard
1. Check if the user exists in Laravel database: `php artisan supabase:list-missing-ids`
2. If missing, create the user: `php artisan make:user {email} {name} {role}`
3. If exists but no Supabase ID, add it: `php artisan supabase:add-id {email} {supabase_id}`

### Sync Failing
1. Check logs: `tail -f storage/logs/laravel.log`
2. Verify Supabase configuration in `.env`
3. Ensure service role key has admin privileges

### Magic Link Issues
1. Check Supabase project settings for correct redirect URLs
2. Verify email templates are configured
3. Check browser console for errors during confirmation

## Complete Sync Process

To sync all existing users:

1. **Get Supabase Users**:
   ```bash
   php scripts/get-supabase-users.php
   ```

2. **Create Missing Users**:
   ```bash
   # Run the commands output by the script above
   php artisan make:user "email@example.com" "Name" "Role" "supabase-id"
   ```

3. **Verify Sync**:
   ```bash
   php artisan supabase:list-missing-ids
   ```

4. **Test Dashboard**:
   - Log in with a user account
   - Check if the dashboard loads correctly
   - Use the "Sync User" button if needed

## Future Improvements

- Set up Supabase webhooks for real-time sync
- Add automatic role assignment based on user metadata
- Implement batch sync for large user bases
- Add sync status monitoring and alerts 