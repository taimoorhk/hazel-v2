-- Add preferred_voice column to elderly_profiles table
ALTER TABLE elderly_profiles 
ADD COLUMN IF NOT EXISTS preferred_voice VARCHAR(50) DEFAULT 'sage';

-- Add comment to document the column
COMMENT ON COLUMN elderly_profiles.preferred_voice IS 'Preferred voice for text-to-speech: sage, alloy, echo, fable, onyx, nova, shimmer';
