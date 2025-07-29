-- Check if the migration has been applied
-- Run this in your Supabase SQL Editor

-- 1. Check if triggers exist
SELECT 
    trigger_name,
    event_manipulation,
    event_object_table,
    action_statement
FROM information_schema.triggers 
WHERE trigger_name IN ('on_auth_user_created', 'on_auth_user_updated', 'on_auth_user_deleted')
AND event_object_schema = 'auth';

-- 2. Check if functions exist
SELECT 
    routine_name,
    routine_type,
    routine_schema
FROM information_schema.routines 
WHERE routine_name IN ('handle_new_user', 'handle_user_update', 'handle_user_delete')
AND routine_schema = 'public';

-- 3. Check if public.users table exists and has the right structure
SELECT 
    column_name,
    data_type,
    is_nullable,
    column_default
FROM information_schema.columns 
WHERE table_name = 'users' 
AND table_schema = 'public'
ORDER BY ordinal_position;

-- 4. Check if real-time is enabled for public.users
SELECT 
    schemaname,
    tablename,
    attname,
    atttypid::regtype as data_type
FROM pg_attribute a
JOIN pg_class c ON a.attrelid = c.oid
JOIN pg_namespace n ON c.relnamespace = n.oid
WHERE n.nspname = 'public' 
AND c.relname = 'users'
AND a.attname = 'supabase_id';

-- 5. Check current users in auth.users vs public.users
SELECT 
    'auth.users' as table_name,
    COUNT(*) as user_count
FROM auth.users
UNION ALL
SELECT 
    'public.users' as table_name,
    COUNT(*) as user_count
FROM public.users; 