-- Create subscriptions table
CREATE TABLE IF NOT EXISTS public.subscriptions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    user_email TEXT NOT NULL,
    stripe_id TEXT UNIQUE NOT NULL,
    stripe_status TEXT NOT NULL,
    stripe_price TEXT,
    quantity INTEGER,
    trial_ends_at TIMESTAMPTZ,
    ends_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create subscription_items table
CREATE TABLE IF NOT EXISTS public.subscription_items (
    id BIGSERIAL PRIMARY KEY,
    subscription_id TEXT NOT NULL,
    stripe_id TEXT UNIQUE NOT NULL,
    stripe_product TEXT NOT NULL,
    stripe_price TEXT NOT NULL,
    quantity INTEGER,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_subscriptions_user_id ON public.subscriptions(user_id);
CREATE INDEX IF NOT EXISTS idx_subscriptions_stripe_id ON public.subscriptions(stripe_id);
CREATE INDEX IF NOT EXISTS idx_subscriptions_stripe_status ON public.subscriptions(stripe_status);
CREATE INDEX IF NOT EXISTS idx_subscription_items_subscription_id ON public.subscription_items(subscription_id);
CREATE INDEX IF NOT EXISTS idx_subscription_items_stripe_id ON public.subscription_items(stripe_id);

-- Enable Row Level Security (RLS)
ALTER TABLE public.subscriptions ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.subscription_items ENABLE ROW LEVEL SECURITY;

-- Create RLS policies for subscriptions
CREATE POLICY "Users can view their own subscriptions" ON public.subscriptions
    FOR SELECT USING (auth.jwt() ->> 'email' = user_email);

CREATE POLICY "Users can insert their own subscriptions" ON public.subscriptions
    FOR INSERT WITH CHECK (auth.jwt() ->> 'email' = user_email);

CREATE POLICY "Users can update their own subscriptions" ON public.subscriptions
    FOR UPDATE USING (auth.jwt() ->> 'email' = user_email);

-- Create RLS policies for subscription_items
CREATE POLICY "Users can view their own subscription items" ON public.subscription_items
    FOR SELECT USING (
        EXISTS (
            SELECT 1 FROM public.subscriptions s 
            WHERE s.stripe_id = subscription_id 
            AND s.user_email = auth.jwt() ->> 'email'
        )
    );

CREATE POLICY "Users can insert their own subscription items" ON public.subscription_items
    FOR INSERT WITH CHECK (
        EXISTS (
            SELECT 1 FROM public.subscriptions s 
            WHERE s.stripe_id = subscription_id 
            AND s.user_email = auth.jwt() ->> 'email'
        )
    );

CREATE POLICY "Users can update their own subscription items" ON public.subscription_items
    FOR UPDATE USING (
        EXISTS (
            SELECT 1 FROM public.subscriptions s 
            WHERE s.stripe_id = subscription_id 
            AND s.user_email = auth.jwt() ->> 'email'
        )
    );

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_subscriptions_updated_at 
    BEFORE UPDATE ON public.subscriptions 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_subscription_items_updated_at 
    BEFORE UPDATE ON public.subscription_items 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
