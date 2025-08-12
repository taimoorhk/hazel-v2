<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
          </div>
          
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Cancelled</h1>
          <p class="text-lg text-gray-600 mb-8">Your subscription was not completed. You can try again anytime.</p>
          
          <div class="bg-white rounded-lg shadow p-6 max-w-md mx-auto mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">What happened?</h2>
            <div class="space-y-3 text-left text-gray-600">
              <p>• You cancelled the payment process</p>
              <p>• There was an issue with your payment method</p>
              <p>• The session expired</p>
            </div>
          </div>
          
          <div class="space-x-4">
            <Button @click="goToBilling" class="bg-blue-600 hover:bg-blue-700">
              Try Again
            </Button>
            <Button @click="goToDashboard" variant="outline">
              Go to Dashboard
            </Button>
          </div>
          
          <p class="text-sm text-gray-500 mt-8">
            Need help? Contact our support team for assistance.
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';

const goToDashboard = () => {
  router.visit(route('dashboard'));
};

const goToBilling = () => {
  router.visit(route('billing.index'));
};
</script>
