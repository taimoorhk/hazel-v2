<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg p-6">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Billing & Subscription</h1>
            <p class="text-gray-600 mb-8">Manage your subscription and payment methods</p>
          </div>

          <!-- Current Subscription Status -->
          <div v-if="subscription" class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Subscription</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <p class="text-lg font-semibold" :class="getStatusColor(subscription.status)">
                  {{ formatStatus(subscription.status) }}
                </p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Plan</p>
                <p class="text-lg font-semibold text-gray-900">{{ subscription.price || 'Premium Plan' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Started</p>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(subscription.created_at) }}</p>
              </div>
              <div v-if="subscription.ends_at">
                <p class="text-sm font-medium text-gray-500">Ends</p>
                <p class="text-lg font-semibold text-gray-900">{{ formatDate(subscription.ends_at) }}</p>
              </div>
            </div>

            <!-- Subscription Actions -->
            <div class="mt-6 flex gap-3">
              <Button 
                v-if="subscription.status === 'active'"
                @click="cancelSubscription"
                variant="outline"
                :disabled="isLoading"
              >
                Cancel Subscription
              </Button>
              <Button 
                v-if="subscription.status === 'canceled'"
                @click="resumeSubscription"
                variant="outline"
                :disabled="isLoading"
              >
                Resume Subscription
              </Button>
            </div>
          </div>

          <!-- No Subscription -->
          <div v-else class="bg-white rounded-lg shadow p-6 mb-6 text-center">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">No Active Subscription</h2>
            <p class="text-gray-600 mb-6">Get started with our premium features</p>
            <Button @click="startSubscription" :disabled="isLoading">
              {{ isLoading ? 'Loading...' : 'Subscribe Now' }}
            </Button>
          </div>

          <!-- Payment Method -->
          <div v-if="paymentMethod" class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
            <div class="flex items-center gap-4">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center">
                  <span class="text-sm font-semibold">{{ paymentMethod.card?.brand?.charAt(0).toUpperCase() }}</span>
                </div>
                <span class="text-gray-900">•••• {{ paymentMethod.card?.last4 }}</span>
              </div>
              <span class="text-gray-500">Expires {{ paymentMethod.card?.exp_month }}/{{ paymentMethod.card?.exp_year }}</span>
            </div>
          </div>

          <!-- Pricing Plans -->
          <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Pricing Plans</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Basic Plan -->
              <div class="border rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Basic</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4">$0<span class="text-sm text-gray-500">/month</span></p>
                <ul class="text-sm text-gray-600 mb-6 space-y-2">
                  <li>✓ Basic features</li>
                  <li>✓ Limited conversations</li>
                  <li>✓ Community support</li>
                </ul>
                <Button variant="outline" disabled>Current Plan</Button>
              </div>

              <!-- Premium Plan -->
              <div class="border-2 border-blue-500 rounded-lg p-6 text-center bg-blue-50">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Premium</h3>
                <p class="text-3xl font-bold text-blue-600 mb-4">$29<span class="text-sm text-gray-500">/month</span></p>
                <ul class="text-sm text-gray-600 mb-6 space-y-2">
                  <li>✓ All basic features</li>
                  <li>✓ Unlimited conversations</li>
                  <li>✓ Priority support</li>
                  <li>✓ Advanced analytics</li>
                </ul>
                <Button 
                  v-if="!subscription || subscription.status !== 'active'"
                  @click="startSubscription"
                  :disabled="isLoading"
                  class="w-full"
                >
                  {{ isLoading ? 'Loading...' : 'Subscribe Now' }}
                </Button>
                <Button v-else variant="outline" disabled class="w-full">Current Plan</Button>
              </div>

              <!-- Enterprise Plan -->
              <div class="border rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Enterprise</h3>
                <p class="text-3xl font-bold text-gray-900 mb-4">$99<span class="text-sm text-gray-500">/month</span></p>
                <ul class="text-sm text-gray-600 mb-6 space-y-2">
                  <li>✓ All premium features</li>
                  <li>✓ Custom integrations</li>
                  <li>✓ Dedicated support</li>
                  <li>✓ White-label options</li>
                </ul>
                <Button variant="outline" class="w-full">Contact Sales</Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '../layouts/AppLayout.vue';
import { Button } from '../components/ui/button';

interface Subscription {
  id: number;
  stripe_id: string;
  status: string;
  price: string;
  quantity: number;
  trial_ends_at: string | null;
  ends_at: string | null;
  created_at: string;
}

interface PaymentMethod {
  type: string;
  card?: {
    brand: string;
    last4: string;
    exp_month: number;
    exp_year: number;
  };
}

const isLoading = ref(false);
const subscription = ref<Subscription | null>(null);
const paymentMethod = ref<PaymentMethod | null>(null);
const stripeKey = ref('');

// Mock data for now - in a real app, this would come from an API
const mockSubscription: Subscription = {
  id: 1,
  stripe_id: 'sub_mock123',
  status: 'active',
  price: 'Premium Plan',
  quantity: 1,
  trial_ends_at: null,
  ends_at: null,
  created_at: new Date().toISOString(),
};

const mockPaymentMethod: PaymentMethod = {
  type: 'card',
  card: {
    brand: 'visa',
    last4: '4242',
    exp_month: 12,
    exp_year: 2025,
  },
};

const formatStatus = (status: string): string => {
  const statusMap: Record<string, string> = {
    'active': 'Active',
    'canceled': 'Canceled',
    'past_due': 'Past Due',
    'unpaid': 'Unpaid',
    'trialing': 'Trial',
  };
  return statusMap[status] || status;
};

const getStatusColor = (status: string): string => {
  const colorMap: Record<string, string> = {
    'active': 'text-green-600',
    'canceled': 'text-red-600',
    'past_due': 'text-yellow-600',
    'unpaid': 'text-red-600',
    'trialing': 'text-blue-600',
  };
  return colorMap[status] || 'text-gray-600';
};

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString();
};

const startSubscription = async () => {
  isLoading.value = true;
  try {
    const response = await fetch(route('stripe.create-checkout-session'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    const data = await response.json();
    
    if (data.sessionId) {
      // Redirect to Stripe Checkout
      const stripe = (window as any).Stripe(stripeKey.value);
      stripe.redirectToCheckout({
        sessionId: data.sessionId,
      });
    } else {
      console.error('Failed to create checkout session');
    }
  } catch (error) {
    console.error('Error starting subscription:', error);
  } finally {
    isLoading.value = false;
  }
};

const cancelSubscription = async () => {
  if (!confirm('Are you sure you want to cancel your subscription?')) return;
  
  isLoading.value = true;
  try {
    const response = await fetch(route('billing.cancel-subscription'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    if (response.ok) {
      router.reload();
    } else {
      console.error('Failed to cancel subscription');
    }
  } catch (error) {
    console.error('Error canceling subscription:', error);
  } finally {
    isLoading.value = false;
  }
};

const resumeSubscription = async () => {
  isLoading.value = true;
  try {
    const response = await fetch(route('billing.resume-subscription'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    if (response.ok) {
      router.reload();
    } else {
      console.error('Failed to resume subscription');
    }
  } catch (error) {
    console.error('Error resuming subscription:', error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  // Load Stripe.js
  const script = document.createElement('script');
  script.src = 'https://js.stripe.com/v3/';
  script.onload = () => {
    // Stripe is now available globally
  };
  document.head.appendChild(script);
  
  // Initialize with mock data for now
  subscription.value = mockSubscription;
  paymentMethod.value = mockPaymentMethod;
  stripeKey.value = 'pk_test_your_publishable_key_here'; // This should come from environment
  
  // TODO: In a real app, fetch actual subscription data from an API
  // fetchSubscriptionData();
});

// Declare Stripe global for TypeScript
declare global {
  interface Window {
    Stripe: any;
  }
}

// Declare route function for TypeScript
declare function route(name: string, params?: any): string;
</script> 