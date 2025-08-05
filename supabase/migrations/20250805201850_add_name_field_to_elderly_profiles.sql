-- Add name field to elderly_profiles table
ALTER TABLE public.elderly_profiles 
ADD COLUMN IF NOT EXISTS name VARCHAR(255);

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_elderly_profiles_name ON public.elderly_profiles(name); 