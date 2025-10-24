<template>
  <AppLayout>
    <Head title="Test Weekly Summary API" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Weekly Summary API Test</h1>
          <p class="mt-2 text-gray-600">Testing real API data fetching for different account/profile combinations</p>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Configuration</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Account ID</label>
              <select v-model="selectedAccountId" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="6">Account 6 (mtaimoorhas1@gmail.com)</option>
                <option value="7">Account 7 (microassetsmain@gmail.com)</option>
                <option value="101">Account 101 (Test Account)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Profile ID</label>
              <select v-model="selectedProfileId" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="-1">Main Profile (-1)</option>
                <option value="15">Elderly Profile (15)</option>
                <option value="101">Test Profile (101)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Data Source</label>
              <select v-model="useMockData" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option :value="false">Real API Data</option>
                <option :value="true">Mock Data</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex justify-center">
            <button 
              @click="refreshData" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Refresh Data
            </button>
          </div>
        </div>

        <!-- Current Configuration Display -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
          <div class="flex items-center space-x-4 text-sm">
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">Account ID:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ selectedAccountId }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">Profile ID:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ selectedProfileId }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">Data Source:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ useMockData ? 'Mock Data' : 'Real API' }}</span>
            </div>
          </div>
        </div>

        <!-- Weekly Summary Cards -->
        <WeeklySummaryCards 
          :account-id="parseInt(selectedAccountId)" 
          :profile-id="parseInt(selectedProfileId)" 
          :time-range-weeks="1"
          :auto-refresh="true"
          :refresh-interval="86400000"
          :use-mock-data="useMockData"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import WeeklySummaryCards from '@/components/WeeklySummaryCards.vue'

// State
const selectedAccountId = ref('6')
const selectedProfileId = ref('-1')
const useMockData = ref(false)

// Methods
const refreshData = () => {
  // Force refresh by updating the key
  console.log('Refreshing data for:', {
    accountId: selectedAccountId.value,
    profileId: selectedProfileId.value,
    useMockData: useMockData.value
  })
}
</script>
