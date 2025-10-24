<template>
  <div class="weekly-summary-dashboard">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Weekly Summary Dashboard</h1>
              <p class="mt-1 text-sm text-gray-600">
                AI-generated insights from your conversations
              </p>
            </div>
            <div class="flex items-center space-x-4">
              <!-- User Selection -->
              <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Account:</label>
                <select
                  v-model="selectedAccountId"
                  @change="onUserChange"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="6">Account 6</option>
                  <option value="7">Account 7</option>
                  <option value="8">Account 8</option>
                </select>
              </div>
              
              <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Profile:</label>
                <select
                  v-model="selectedProfileId"
                  @change="onUserChange"
                  class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="-1">Main Account</option>
                  <option value="15">Elderly Profile 15</option>
                  <option value="16">Elderly Profile 16</option>
                </select>
              </div>

              <!-- Refresh Button -->
              <button
                @click="refreshSummary"
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
              >
                <svg class="w-4 h-4 mr-2" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ loading ? 'Generating...' : 'Refresh' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- API Status -->
      <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">API Status</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 bg-green-400 rounded-full"></div>
              <span class="text-sm text-gray-600">Weekly Summary API</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 bg-green-400 rounded-full"></div>
              <span class="text-sm text-gray-600">Health Check</span>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-3 h-3 bg-green-400 rounded-full"></div>
              <span class="text-sm text-gray-600">Real-time Sync</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Weekly Summary Cards -->
      <WeeklySummaryCards
        :account-id="selectedAccountId"
        :profile-id="selectedProfileId"
        :time-range-weeks="1"
        :auto-refresh="true"
        :refresh-interval="300000"
        :use-mock-data="useMockData"
      />

      <!-- API Configuration -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Configuration</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Data Source</label>
              <div class="flex items-center space-x-4">
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="useMockData"
                    :value="true"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">Mock Data (Development)</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="radio"
                    v-model="useMockData"
                    :value="false"
                    class="mr-2"
                  />
                  <span class="text-sm text-gray-700">Real API</span>
                </label>
              </div>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Time Range</label>
              <select
                v-model="timeRangeWeeks"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="1">1 Week</option>
                <option value="2">2 Weeks</option>
                <option value="4">4 Weeks</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- API Endpoints Info -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">API Endpoints</h2>
          <div class="space-y-4">
            <div class="rounded-lg p-4" style="background-color: #f9fafb;">
              <h3 class="font-medium text-gray-900 mb-2">Weekly Summary Endpoint</h3>
              <code class="text-sm text-gray-600">
                POST http://143.198.187.46:8001/conversations/weekly-summary
              </code>
              <p class="text-sm text-gray-500 mt-1">
                Generates AI-written weekly reflection summary based on recent conversations
              </p>
            </div>
            
            <div class="rounded-lg p-4" style="background-color: #f9fafb;">
              <h3 class="font-medium text-gray-900 mb-2">Health Check Endpoint</h3>
              <code class="text-sm text-gray-600">
                GET http://143.198.187.46:8001/health
              </code>
              <p class="text-sm text-gray-500 mt-1">
                Verifies that the backend service is running and reachable
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import WeeklySummaryCards from '@/components/WeeklySummaryCards.vue'

// State
const selectedAccountId = ref(6)
const selectedProfileId = ref(-1)
const timeRangeWeeks = ref(1)
const useMockData = ref(true)
const loading = ref(false)

// Methods
const onUserChange = () => {
  // User selection changed, the WeeklySummaryCards component will automatically update
  console.log('User changed:', selectedAccountId.value, selectedProfileId.value)
}

const refreshSummary = () => {
  // Trigger refresh in the WeeklySummaryCards component
  loading.value = true
  setTimeout(() => {
    loading.value = false
  }, 1000)
}

onMounted(() => {
  // Initialize dashboard
  console.log('Weekly Summary Dashboard mounted')
})
</script>

<style scoped>
.weekly-summary-dashboard {
  min-height: 100vh;
  background-color: #f9fafb;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
