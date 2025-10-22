<template>
  <AppLayout>
    <Head title="Dashboard" />
    
    <div class="dashboard-container">
      <!-- Welcome Section -->
      <div class="welcome-section">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Welcome, {{ user?.user_metadata?.name || 'User' }}!
            </h1>
            <p class="mt-1 text-sm text-gray-600">
              Caregiver dashboard overview.
            </p>
          </div>
          <button
            @click="goToElderlyProfiles"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            + Add Elderly Profile
          </button>
        </div>
      </div>

      <!-- Health Analytics Dashboard -->
      <div class="health-analytics-section">
        <div class="flex items-center space-x-3 mb-6">
          <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Health Analytics Dashboard</h2>
            <p class="text-sm text-gray-600">Health monitoring and analytics for all profiles.</p>
          </div>
        </div>

        <!-- Weekly Summary Cards -->
        <WeeklySummaryCards 
          :account-id="accountId" 
          :profile-id="profileId" 
          :time-range-weeks="1"
          :auto-refresh="true"
          :refresh-interval="300000"
          :use-mock-data="true"
        />

        <!-- Health Risk Assessment -->
        <div class="mt-8">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Risk Assessment</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Alzheimer's Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">1.5/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Parkinson's Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">2/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709M15 6.291A7.962 7.962 0 0012 4c-2.34 0-4.29 1.009-5.824 2.709" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Depression Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">1.8/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Anxiety Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">1.2/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Fall Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">2.5/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">Cognitive Risk</h4>
                  <p class="text-2xl font-bold text-gray-900">2/10</p>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Low Risk
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { ref, onMounted } from 'vue';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import WeeklySummaryCards from '@/components/WeeklySummaryCards.vue';
import { router } from '@inertiajs/vue3';

// Auth guard
useAuthGuard();

// Get user data
const { user } = useSupabaseUser();

// Get account and profile IDs based on user email
function getUserAccountId() {
  const currentUser = user.value;
  if (!currentUser?.email) {
    return 6; // Default fallback
  }
  
  const emailToAccountId = {
    'mtaimoorhas1@gmail.com': 6,
    'jsahib@gmail.com': 6, // This is an elderly profile under account 6
    'microassetsmain@gmail.com': 7, // New account ID for microassetsmain
    // Add more mappings as needed
  };
  
  return emailToAccountId[currentUser.email] || 6; // Default to account 6
}

function getUserProfileId() {
  const currentUser = user.value;
  if (currentUser?.email) {
    if (currentUser.email === 'jsahib@gmail.com') {
      return 15; // Elderly profile ID
    }
    return -1; // Main account holder profile ID
  }
  return -1; // Default fallback
}

// Reactive data
const accountId = ref(getUserAccountId());
const profileId = ref(getUserProfileId());

// Methods
const goToElderlyProfiles = () => {
  router.visit(route('elderly-profiles'));
};

// Update account/profile IDs when user changes
onMounted(() => {
  accountId.value = getUserAccountId();
  profileId.value = getUserProfileId();
});
</script>

<style scoped>
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px;
}

.welcome-section {
  margin-bottom: 32px;
}

.health-analytics-section {
  margin-bottom: 32px;
}
</style>
