<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class StripeIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that Stripe routes are accessible
     */
    public function test_stripe_routes_exist(): void
    {
        $response = $this->get('/stripe/webhook');
        $this->assertNotEquals(404, $response->status());
    }

    /**
     * Test that billing routes are accessible
     */
    public function test_billing_routes_exist(): void
    {
        $response = $this->get('/billing');
        $this->assertNotEquals(404, $response->status());
    }

    /**
     * Test that User model has Billable trait
     */
    public function test_user_model_has_billable_trait(): void
    {
        $user = new User();
        $this->assertTrue(method_exists($user, 'subscription'));
        $this->assertTrue(method_exists($user, 'hasDefaultPaymentMethod'));
    }

    /**
     * Test Stripe configuration is loaded
     */
    public function test_stripe_config_is_loaded(): void
    {
        $this->assertNotNull(config('services.stripe.key'));
        $this->assertNotNull(config('services.stripe.secret'));
    }
}
