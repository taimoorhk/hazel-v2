-- Add preferred_voice column to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS preferred_voice VARCHAR(50) DEFAULT 'sage';

-- Add comment to document the column
COMMENT ON COLUMN users.preferred_voice IS 'Preferred voice for text-to-speech: sage, alloy, echo, fable, onyx, nova, shimmer';
