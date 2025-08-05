-- Create elderly_profiles table in Supabase public schema
CREATE TABLE IF NOT EXISTS public.elderly_profiles (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    temporary_role VARCHAR(50) DEFAULT 'elderly user',
    associated_account_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW()
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_elderly_profiles_email ON public.elderly_profiles(email);
CREATE INDEX IF NOT EXISTS idx_elderly_profiles_phone ON public.elderly_profiles(phone);
CREATE INDEX IF NOT EXISTS idx_elderly_profiles_associated_account ON public.elderly_profiles(associated_account_email);

-- Enable real-time for the elderly_profiles table
ALTER PUBLICATION supabase_realtime ADD TABLE public.elderly_profiles;

-- Enable Row Level Security
ALTER TABLE public.elderly_profiles ENABLE ROW LEVEL SECURITY;

-- Create policies for the elderly_profiles table
-- Users can only view elderly profiles associated with their account
CREATE POLICY "Users can view their own elderly profiles" ON public.elderly_profiles
    FOR SELECT USING (auth.jwt() ->> 'email' = associated_account_email);

-- Users can insert elderly profiles for their own account
CREATE POLICY "Users can insert their own elderly profiles" ON public.elderly_profiles
    FOR INSERT WITH CHECK (auth.jwt() ->> 'email' = associated_account_email);

-- Users can update elderly profiles associated with their account
CREATE POLICY "Users can update their own elderly profiles" ON public.elderly_profiles
    FOR UPDATE USING (auth.jwt() ->> 'email' = associated_account_email);

-- Users can delete elderly profiles associated with their account
CREATE POLICY "Users can delete their own elderly profiles" ON public.elderly_profiles
    FOR DELETE USING (auth.jwt() ->> 'email' = associated_account_email);

-- Allow service role to manage all elderly profiles
CREATE POLICY "Service role can manage all elderly profiles" ON public.elderly_profiles
    FOR ALL USING (auth.role() = 'service_role');

-- Create a function to automatically update the updated_at timestamp
CREATE OR REPLACE FUNCTION public.update_elderly_profiles_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to automatically update updated_at
DROP TRIGGER IF EXISTS update_elderly_profiles_updated_at ON public.elderly_profiles;
CREATE TRIGGER update_elderly_profiles_updated_at
    BEFORE UPDATE ON public.elderly_profiles
    FOR EACH ROW EXECUTE FUNCTION public.update_elderly_profiles_updated_at(); 