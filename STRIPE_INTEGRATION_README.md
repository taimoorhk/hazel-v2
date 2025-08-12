# Stripe Integration with Real-time Supabase Sync

This project integrates Stripe payments with real-time synchronization to Supabase database. The integration handles subscriptions, payment methods, and webhooks to ensure data consistency across both systems.

## 🚀 Features

- **Stripe Checkout Integration**: Seamless subscription checkout using Stripe Checkout
- **Real-time Sync**: Automatic synchronization of subscription data to Supabase
- **Webhook Handling**: Comprehensive webhook processing for all Stripe events
- **Subscription Management**: Cancel, resume, and update subscription plans
- **Payment Method Management**: Secure handling of payment methods
- **Multi-plan Support**: Basic, Premium, and Enterprise plan options

## 📋 Prerequisites

- Laravel 12.x application
- Supabase project with authentication enabled
- Stripe account with API access
- Laravel Cashier package (already installed)

## 🔧 Setup Instructions

### 1. Environment Configuration

Add the following variables to your `.env` file:

```bash
# Stripe Configuration
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
STRIPE_PRICE_ID=price_your_price_id_here
STRIPE_PRODUCT_ID=prod_your_product_id_here

# Supabase Configuration (already configured)
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key
```

### 2. Stripe Dashboard Setup

1. **Create Products and Prices**:
   - Go to [Stripe Dashboard](https://dashboard.stripe.com/)
   - Create a product (e.g., "Premium Plan")
   - Set up recurring pricing (e.g., $29/month)
   - Copy the Price ID to `STRIPE_PRICE_ID`

2. **Configure Webhooks**:
   - Go to Developers → Webhooks
   - Add endpoint: `https://yourdomain.com/stripe/webhook`
   - Select events:
     - `checkout.session.completed`
     - `customer.subscription.created`
     - `customer.subscription.updated`
     - `customer.subscription.deleted`
     - `invoice.payment_succeeded`
     - `invoice.payment_failed`
   - Copy the webhook secret to `STRIPE_WEBHOOK_SECRET`

### 3. Database Setup

The subscription tables are already created. Run migrations if needed:

```bash
php artisan migrate
```

### 4. Supabase Migration

Run the Supabase migration to create subscription tables:

```bash
# Apply the migration to your Supabase project
# The file is located at: supabase/migrations/20250808160000_create_subscriptions_table.sql
```

## 🗄️ Database Schema

### Laravel Tables (already exist)
- `users` - User information with Stripe customer ID
- `subscriptions` - Subscription details
- `subscription_items` - Individual subscription items

### Supabase Tables
- `public.subscriptions` - Mirrored subscription data
- `public.subscription_items` - Mirrored subscription items

## 🔄 Real-time Sync

The system automatically syncs data between Laravel and Supabase:

- **Webhook Events**: All Stripe webhooks trigger immediate sync
- **Manual Sync**: Use artisan commands for bulk operations
- **Bidirectional**: Data flows from Laravel to Supabase in real-time

### Sync Commands

```bash
# Sync specific user
php artisan stripe:sync-subscriptions --user=user@example.com

# Sync all users
php artisan stripe:sync-subscriptions --all
```

## 📱 Frontend Components

### Billing Page (`/billing`)
- View current subscription status
- Manage subscription (cancel/resume)
- View payment methods
- Choose subscription plans

### Success/Cancel Pages
- Payment success confirmation
- Payment cancellation handling
- Navigation back to dashboard

## 🔌 API Endpoints

### Stripe Endpoints
- `POST /stripe/create-checkout-session` - Create checkout session
- `POST /stripe/webhook` - Handle Stripe webhooks

### Billing Endpoints
- `GET /billing` - Billing dashboard
- `GET /billing/success` - Payment success page
- `GET /billing/cancel` - Payment cancel page
- `POST /billing/cancel-subscription` - Cancel subscription
- `POST /billing/resume-subscription` - Resume subscription
- `POST /billing/update-payment-method` - Update payment method

## 🛡️ Security Features

- **Webhook Verification**: All webhooks are verified using Stripe signatures
- **Row Level Security**: Supabase tables use RLS policies
- **Authentication**: All billing endpoints require user authentication
- **CSRF Protection**: Laravel CSRF tokens for form submissions

## 🧪 Testing

### Test Cards
Use Stripe test cards for development:

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Requires Authentication**: `4000 0025 0000 3155`

### Test Mode
- Use test API keys during development
- Test webhooks using Stripe CLI
- Monitor logs for sync operations

## 📊 Monitoring

### Logs
Check Laravel logs for sync operations:
```bash
tail -f storage/logs/laravel.log | grep -i stripe
```

### Supabase Dashboard
Monitor real-time data in Supabase:
- Tables: `subscriptions`, `subscription_items`
- Real-time subscriptions enabled
- Row Level Security policies active

## 🚨 Troubleshooting

### Common Issues

1. **Webhook Failures**:
   - Verify webhook secret in `.env`
   - Check webhook endpoint URL
   - Ensure HTTPS for production

2. **Sync Failures**:
   - Verify Supabase credentials
   - Check network connectivity
   - Review Laravel logs for errors

3. **Payment Failures**:
   - Verify Stripe API keys
   - Check test mode vs live mode
   - Validate webhook configuration

### Debug Commands

```bash
# Test Stripe connection
php artisan tinker
>>> \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
>>> \Stripe\Customer::all(['limit' => 1]);

# Test Supabase connection
php artisan tinker
>>> app(\App\Services\StripeRealtimeSyncService::class)->getSubscriptionFromSupabase('test@example.com');
```

## 🔄 Webhook Events Handled

| Event | Action | Sync |
|-------|--------|------|
| `checkout.session.completed` | Create customer | ✅ User data |
| `customer.subscription.created` | Create subscription | ✅ Subscription + Items |
| `customer.subscription.updated` | Update subscription | ✅ Subscription + Items |
| `customer.subscription.deleted` | Cancel subscription | ✅ Subscription status |
| `invoice.payment_succeeded` | Update payment method | ✅ User payment info |
| `invoice.payment_failed` | Handle failure | ✅ User status |

## 📈 Scaling Considerations

- **Queue Jobs**: Consider using queues for bulk sync operations
- **Rate Limiting**: Implement rate limiting for webhook endpoints
- **Caching**: Cache subscription data for frequently accessed information
- **Monitoring**: Set up alerts for sync failures

## 🤝 Support

For issues related to:
- **Stripe Integration**: Check Stripe documentation and logs
- **Supabase Sync**: Review sync service logs and database policies
- **Laravel Issues**: Check Laravel logs and Cashier documentation

## 📚 Additional Resources

- [Stripe Documentation](https://docs.stripe.com/)
- [Laravel Cashier Documentation](https://laravel.com/docs/cashier)
- [Supabase Documentation](https://supabase.com/docs)
- [Stripe Webhook Testing](https://stripe.com/docs/webhooks/test)
