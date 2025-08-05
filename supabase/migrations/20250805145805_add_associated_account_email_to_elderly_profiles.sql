-- Add associated_account_email field to elderly_profiles table
ALTER TABLE public.elderly_profiles 
ADD COLUMN IF NOT EXISTS associated_account_email VARCHAR(255);

-- Update existing records to have a default associated_account_email
-- This will be handled by the application logic for new records
UPDATE public.elderly_profiles 
SET associated_account_email = 'system@default.com' 
WHERE associated_account_email IS NULL;

-- Make the field NOT NULL after setting default values
ALTER TABLE public.elderly_profiles 
ALTER COLUMN associated_account_email SET NOT NULL;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_elderly_profiles_associated_account ON public.elderly_profiles(associated_account_email);

-- Update RLS policies to use the new field
DROP POLICY IF EXISTS "Users can view elderly profiles" ON public.elderly_profiles;
DROP POLICY IF EXISTS "Users can insert elderly profiles" ON public.elderly_profiles;
DROP POLICY IF EXISTS "Users can update elderly profiles" ON public.elderly_profiles;
DROP POLICY IF EXISTS "Users can delete elderly profiles" ON public.elderly_profiles;

-- Create new policies that restrict access based on associated_account_email
CREATE POLICY "Users can view their own elderly profiles" ON public.elderly_profiles
    FOR SELECT USING (auth.jwt() ->> 'email' = associated_account_email);

CREATE POLICY "Users can insert their own elderly profiles" ON public.elderly_profiles
    FOR INSERT WITH CHECK (auth.jwt() ->> 'email' = associated_account_email);

CREATE POLICY "Users can update their own elderly profiles" ON public.elderly_profiles
    FOR UPDATE USING (auth.jwt() ->> 'email' = associated_account_email);

CREATE POLICY "Users can delete their own elderly profiles" ON public.elderly_profiles
    FOR DELETE USING (auth.jwt() ->> 'email' = associated_account_email); 