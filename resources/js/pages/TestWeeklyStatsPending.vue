<template>
  <AppLayout>
    <Head title="Test Weekly Stats Pending" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Weekly Stats Pending Test</h1>
          <p class="mt-2 text-gray-600">Testing "Weekly stats pending" display for profiles without data</p>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Configuration</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Account ID</label>
              <select v-model="selectedAccountId" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="6">Account 6 (mtaimoorhas1@gmail.com)</option>
                <option value="7">Account 7 (microassetsmain@gmail.com)</option>
                <option value="8">Account 8 (test@example.com)</option>
                <option value="101">Account 101 (test account)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Profile ID</label>
              <select v-model="selectedProfileId" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="-1">Main Account (-1)</option>
                <option value="15">Elderly Profile (15)</option>
                <option value="102">Elderly Profile (102)</option>
                <option value="999">Test Profile (999)</option>
              </select>
            </div>
          </div>
          <div class="mt-4 flex justify-center">
            <button 
              @click="testWeeklyStats" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Test Weekly Stats
            </button>
          </div>
        </div>

        <!-- Results -->
        <div v-if="testResult" class="space-y-6">
          <!-- Test Result Summary -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Test Result</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Account ID:</span>
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ testResult.accountId }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Profile ID:</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ testResult.profileId }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Has Specific Data:</span>
                <span :class="testResult.hasSpecificData ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded">
                  {{ testResult.hasSpecificData ? 'YES' : 'NO' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Shows Pending:</span>
                <span :class="testResult.showsPending ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ testResult.showsPending ? 'YES' : 'NO' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Weekly Summary Cards Test -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Summary Cards Test</h3>
            <WeeklySummaryCards 
              :account-id="parseInt(selectedAccountId)" 
              :profile-id="parseInt(selectedProfileId)" 
              :time-range-weeks="1"
              :auto-refresh="false"
              :use-mock-data="true"
            />
          </div>
        </div>

        <!-- Expected Behavior Guide -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-blue-900 mb-4">Expected Behavior</h3>
          <div class="space-y-2 text-sm text-blue-800">
            <div><strong>Account 6, Profile -1:</strong> Should show specific data for mtaimoorhas1@gmail.com</div>
            <div><strong>Account 6, Profile 15:</strong> Should show specific data for elderly profile</div>
            <div><strong>Account 7, Profile -1:</strong> Should show specific data for microassetsmain@gmail.com</div>
            <div><strong>Account 6, Profile 102:</strong> Should show "Weekly stats pending"</div>
            <div><strong>Account 7, Profile 15:</strong> Should show "Weekly stats pending"</div>
            <div><strong>Account 8, Profile -1:</strong> Should show "Weekly stats pending"</div>
            <div><strong>Account 101, Profile 101:</strong> Should show "Weekly stats pending"</div>
          </div>
        </div>
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
const testResult = ref<any>(null)

// Methods
const testWeeklyStats = () => {
  const accountId = parseInt(selectedAccountId.value)
  const profileId = parseInt(selectedProfileId.value)
  
  // Determine if this combination should have specific data
  const hasSpecificData = (
    (accountId === 6 && profileId === -1) || // mtaimoorhas1@gmail.com
    (accountId === 6 && profileId === 15) || // jsahib@gmail.com elderly
    (accountId === 7 && profileId === -1)    // microassetsmain@gmail.com
  )
  
  // Determine if it should show pending
  const showsPending = !hasSpecificData
  
  testResult.value = {
    accountId,
    profileId,
    hasSpecificData,
    showsPending,
    expectedBehavior: hasSpecificData ? 'Should show specific data' : 'Should show "Weekly stats pending"'
  }
  
  console.log('🧪 Testing weekly stats for:', {
    accountId,
    profileId,
    hasSpecificData,
    showsPending
  })
}
</script>
