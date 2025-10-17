-- Add prefix_padding_duration field to public.users table
-- This field controls the duration of padding added before speech processing
-- Range: 0.2 to 1.2 seconds with 0.01 second precision
-- Default: 0.5 seconds for all users

-- Add the new column with constraints
ALTER TABLE public.users 
ADD COLUMN prefix_padding_duration DECIMAL(3,2) NOT NULL DEFAULT 0.5 
CHECK (prefix_padding_duration >= 0.2 AND prefix_padding_duration <= 1.2);

-- Add a comment to document the field
COMMENT ON COLUMN public.users.prefix_padding_duration IS 'Duration of padding added before speech processing (0.2 to 1.2 seconds, 0.01 precision)';

-- Update existing users to have the default value
UPDATE public.users 
SET prefix_padding_duration = 0.5 
WHERE prefix_padding_duration IS NULL;

-- Create an index for better performance on queries involving this field
CREATE INDEX IF NOT EXISTS idx_users_prefix_padding_duration ON public.users(prefix_padding_duration);

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
        min_speech_duration = COALESCE(
            (NEW.raw_user_meta_data->>'min_speech_duration')::DECIMAL(3,2),
            users.min_speech_duration,
            0.05
        ),
        min_silence_duration = COALESCE(
            (NEW.raw_user_meta_data->>'min_silence_duration')::DECIMAL(3,2),
            users.min_silence_duration,
            0.55
        ),
        prefix_padding_duration = COALESCE(
            (NEW.raw_user_meta_data->>'prefix_padding_duration')::DECIMAL(3,2),
            users.prefix_padding_duration,
            0.5
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
    INSERT INTO public.users (name, email, supabase_id, min_endpointing_delay, max_endpointing_delay, min_speech_duration, min_silence_duration, prefix_padding_duration, created_at, updated_at)
    VALUES (
        COALESCE(NEW.raw_user_meta_data->>'name', NEW.raw_user_meta_data->>'display_name', 'User'),
        NEW.email,
        NEW.id,
        COALESCE((NEW.raw_user_meta_data->>'min_endpointing_delay')::DECIMAL(3,1), 0.5),
        COALESCE((NEW.raw_user_meta_data->>'max_endpointing_delay')::DECIMAL(3,1), 6.0),
        COALESCE((NEW.raw_user_meta_data->>'min_speech_duration')::DECIMAL(3,2), 0.05),
        COALESCE((NEW.raw_user_meta_data->>'min_silence_duration')::DECIMAL(3,2), 0.55),
        COALESCE((NEW.raw_user_meta_data->>'prefix_padding_duration')::DECIMAL(3,2), 0.5),
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
        AND column_name = 'prefix_padding_duration'
    ) THEN
        RAISE NOTICE '✅ prefix_padding_duration column added successfully to public.users table';
    ELSE
        RAISE EXCEPTION '❌ Failed to add prefix_padding_duration column';
    END IF;
    
    -- Check if the constraint was applied
    IF EXISTS (
        SELECT 1 FROM information_schema.check_constraints 
        WHERE constraint_name LIKE '%prefix_padding_duration%'
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
        AND column_name = 'prefix_padding_duration'
        AND column_default = '0.5'
    ) THEN
        RAISE NOTICE '✅ Default value 0.5 set successfully';
    ELSE
        RAISE NOTICE '⚠️ Default value may not have been set correctly';
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
