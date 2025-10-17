-- Add max_endpointing_delay field to public.users table
-- This field controls the maximum delay before endpointing (stopping) audio processing
-- Range: 3.0 to 9.0 seconds with 0.5 second divisions
-- Default: 6.0 seconds for all users

-- Add the new column with constraints
ALTER TABLE public.users 
ADD COLUMN max_endpointing_delay DECIMAL(3,1) NOT NULL DEFAULT 6.0 
CHECK (max_endpointing_delay >= 3.0 AND max_endpointing_delay <= 9.0);

-- Add a comment to document the field
COMMENT ON COLUMN public.users.max_endpointing_delay IS 'Maximum delay before endpointing audio processing (3.0 to 9.0 seconds, 0.5 divisions)';

-- Update existing users to have the default value
UPDATE public.users 
SET max_endpointing_delay = 6.0 
WHERE max_endpointing_delay IS NULL;

-- Create an index for better performance on queries involving this field
CREATE INDEX IF NOT EXISTS idx_users_max_endpointing_delay ON public.users(max_endpointing_delay);

-- Update the handle_user_update function to include this field
CREATE OR REPLACE FUNCTION public.handle_user_update()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE public.users 
    SET 
        name = COALESCE(NEW.raw_user_meta_data->>'name', NEW.raw_user_meta_data->>'display_name', users.name),
        email = NEW.email,
        user_questions = NEW.raw_user_meta_data->>'user_questions',
        min_endpointing_delay = COALESCE(
            (NEW.raw_user_meta_data->>'min_endpointing_delay')::DECIMAL(3,1),
            users.min_endpointing_delay,
            0.5
        ),
        max_endpointing_delay = COALESCE(
            (NEW.raw_user_meta_data->>'max_endpointing_delay')::DECIMAL(3,1),
            users.max_endpointing_delay,
            6.0
        ),
        updated_at = NOW()
    WHERE supabase_id = NEW.id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Update the handle_new_user function to include this field
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO public.users (name, email, supabase_id, min_endpointing_delay, max_endpointing_delay, created_at, updated_at)
    VALUES (
        COALESCE(NEW.raw_user_meta_data->>'name', NEW.raw_user_meta_data->>'display_name', 'User'),
        NEW.email,
        NEW.id,
        COALESCE((NEW.raw_user_meta_data->>'min_endpointing_delay')::DECIMAL(3,1), 0.5),
        COALESCE((NEW.raw_user_meta_data->>'max_endpointing_delay')::DECIMAL(3,1), 6.0),
        NOW(),
        NOW()
    );
    RETURN NEW;
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Enable real-time for the updated users table
ALTER PUBLICATION supabase_realtime ADD TABLE public.users;

-- Verify the changes
DO $$
BEGIN
    -- Check if the column was added successfully
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'max_endpointing_delay'
    ) THEN
        RAISE NOTICE '✅ max_endpointing_delay column added successfully to public.users table';
    ELSE
        RAISE EXCEPTION '❌ Failed to add max_endpointing_delay column';
    END IF;
    
    -- Check if the constraint was applied
    IF EXISTS (
        SELECT 1 FROM information_schema.check_constraints 
        WHERE constraint_name LIKE '%max_endpointing_delay%'
    ) THEN
        RAISE NOTICE '✅ Check constraint applied successfully';
    ELSE
        RAISE NOTICE '⚠️ Check constraint may not have been applied';
    END IF;
    
    -- Check if the default value is set
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'max_endpointing_delay'
        AND column_default = '6.0'
    ) THEN
        RAISE NOTICE '✅ Default value 6.0 set successfully';
    ELSE
        RAISE NOTICE '⚠️ Default value may not have been set correctly';
    END IF;
    
    -- Check if the min_speech_duration column exists (will be added by the next migration)
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'min_speech_duration'
    ) THEN
        RAISE NOTICE '✅ min_speech_duration column exists (added by next migration)';
    ELSE
        RAISE NOTICE 'ℹ️ min_speech_duration column will be added by the next migration';
    END IF;
    
    -- Check if the min_silence_duration column exists (will be added by the next migration)
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'min_silence_duration'
    ) THEN
        RAISE NOTICE '✅ min_silence_duration column exists (added by next migration)';
    ELSE
        RAISE NOTICE 'ℹ️ min_silence_duration column will be added by the next migration';
    END IF;
    
    -- Check if the prefix_padding_duration column exists (will be added by the next migration)
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'prefix_padding_duration'
    ) THEN
        RAISE NOTICE '✅ prefix_padding_duration column exists (added by next migration)';
    ELSE
        RAISE NOTICE 'ℹ️ prefix_padding_duration column will be added by the next migration';
    END IF;
    
    -- Check if the max_buffered_speech column exists (will be added by the next migration)
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'max_buffered_speech'
    ) THEN
        RAISE NOTICE '✅ max_buffered_speech column exists (added by next migration)';
    ELSE
        RAISE NOTICE 'ℹ️ max_buffered_speech column will be added by the next migration';
    END IF;
    
    -- Check if the activation_threshold column exists (will be added by the next migration)
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'users' 
        AND column_name = 'activation_threshold'
    ) THEN
        RAISE NOTICE '✅ activation_threshold column exists (added by next migration)';
    ELSE
        RAISE NOTICE 'ℹ️ activation_threshold column will be added by the next migration';
    END IF;
END $$;
