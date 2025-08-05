-- Add status field to elderly_profiles table
ALTER TABLE public.elderly_profiles 
ADD COLUMN IF NOT EXISTS status VARCHAR(50) DEFAULT 'active';

-- Update existing records to have 'active' status
UPDATE public.elderly_profiles 
SET status = 'active' 
WHERE status IS NULL;

-- Make status field NOT NULL after setting default values
ALTER TABLE public.elderly_profiles 
ALTER COLUMN status SET NOT NULL; 