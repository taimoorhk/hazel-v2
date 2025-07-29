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

### 5. Sync Existing Users with Supabase (NEW)
This command creates Supabase users for existing Laravel users who don't have Supabase IDs:
```bash
# Sync all users without Supabase IDs
php artisan supabase:sync-existing-users

# Sync specific user
php artisan supabase:sync-existing-users --email=user@example.com
```

### 6. Dispatch Background Sync Job
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