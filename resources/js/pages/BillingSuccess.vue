<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
          <p class="text-lg text-gray-600 mb-8">Thank you for subscribing to our premium plan.</p>
          
          <div class="bg-white rounded-lg shadow p-6 max-w-md mx-auto mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription Details</h2>
            <div class="space-y-3 text-left">
              <div class="flex justify-between">
                <span class="text-gray-600">Session ID:</span>
                <span class="font-mono text-sm text-gray-900">{{ sessionId }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="text-green-600 font-semibold">Active</span>
              </div>
            </div>
          </div>
          
          <div class="space-x-4">
            <Button @click="goToDashboard" class="bg-blue-600 hover:bg-blue-700">
              Go to Dashboard
            </Button>
            <Button @click="goToBilling" variant="outline">
              Manage Billing
            </Button>
          </div>
          
          <p class="text-sm text-gray-500 mt-8">
            You will receive a confirmation email shortly. If you have any questions, please contact our support team.
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

// interface Props {
//   sessionId: string;
// }

// const props = defineProps<Props>();

const goToDashboard = () => {
  router.visit(route('dashboard'));
};

const goToBilling = () => {
  router.visit(route('billing'));
};
</script>
