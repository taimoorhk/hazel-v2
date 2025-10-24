<template>
  <AppLayout>
    <Head title="Test Canary Data Pending" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Canary Data Pending Test</h1>
          <p class="mt-2 text-gray-600">Testing Health Risk Assessment "pending" display for profiles without canary data</p>
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
              @click="testCanaryData" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Test Canary Data
            </button>
          </div>
        </div>

        <!-- Test Results -->
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
                <span class="font-medium">Has Canary Data:</span>
                <span :class="testResult.hasCanaryData ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded">
                  {{ testResult.hasCanaryData ? 'YES' : 'NO' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Shows Pending:</span>
                <span :class="testResult.showsPending ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ testResult.showsPending ? 'YES' : 'NO' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">API Response:</span>
                <span :class="testResult.apiSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ testResult.apiSuccess ? 'Success' : 'Failed' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Error:</span>
                <span class="text-red-600">{{ testResult.error || 'None' }}</span>
              </div>
            </div>
          </div>

          <!-- Risk Scores Display -->
          <div v-if="testResult.riskScores" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Risk Assessment Scores</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
              <div class="flex justify-between">
                <span class="font-medium">Alzheimer Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.alzheimerRisk }}/10</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Parkinson Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.parkinsonRisk }}/10</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Depression Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.depressionRisk }}/10</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Anxiety Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.anxietyRisk }}/10</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Fall Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.fallRisk }}/10</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Cognitive Risk:</span>
                <span class="font-mono">{{ testResult.riskScores.cognitiveRisk }}/10</span>
              </div>
            </div>
          </div>

          <!-- Clean Working Dashboard Test -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Risk Assessment Dashboard Test</h3>
            <CleanWorkingDashboard 
              :profile-id="parseInt(selectedProfileId)" 
              :account-id="parseInt(selectedAccountId)"
              :is-elderly-profile="parseInt(selectedProfileId) !== -1"
            />
          </div>
        </div>

        <!-- Expected Behavior Guide -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-blue-900 mb-4">Expected Behavior</h3>
          <div class="space-y-2 text-sm text-blue-800">
            <div><strong>Account 6, Profile -1:</strong> Should show real canary data for mtaimoorhas1@gmail.com</div>
            <div><strong>Account 6, Profile 15:</strong> Should show real canary data for elderly profile</div>
            <div><strong>Account 7, Profile -1:</strong> Should show real canary data for microassetsmain@gmail.com</div>
            <div><strong>Account 6, Profile 102:</strong> Should show "Stats Pending" (0/10 scores)</div>
            <div><strong>Account 7, Profile 15:</strong> Should show "Stats Pending" (0/10 scores)</div>
            <div><strong>Account 8, Profile -1:</strong> Should show "Stats Pending" (0/10 scores)</div>
            <div><strong>Account 101, Profile 101:</strong> Should show "Stats Pending" (0/10 scores)</div>
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
import CleanWorkingDashboard from '@/components/CleanWorkingDashboard.vue'

// State
const selectedAccountId = ref('6')
const selectedProfileId = ref('-1')
const testResult = ref<any>(null)

// Methods
const testCanaryData = async () => {
  const accountId = parseInt(selectedAccountId.value)
  const profileId = parseInt(selectedProfileId.value)
  
  try {
    console.log('🧪 Testing canary data for:', { accountId, profileId })
    
    // Test the API endpoint
    const response = await fetch('/api/realtime-sync/profile-data', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        account_id: accountId,
        profile_id: profileId
      })
    })

    const result = await response.json()
    
    const hasCanaryData = result.success && result.data?.has_data && result.data?.canary_analysis_files?.length > 0
    const showsPending = !hasCanaryData
    
    const riskScores = result.data?.aggregated_health_summary ? {
      alzheimerRisk: result.data.aggregated_health_summary.alzheimer_risk_score || 0,
      parkinsonRisk: result.data.aggregated_health_summary.parkinson_risk_score || 0,
      depressionRisk: result.data.aggregated_health_summary.depression_risk_score || 0,
      anxietyRisk: result.data.aggregated_health_summary.anxiety_risk_score || 0,
      fallRisk: result.data.aggregated_health_summary.fall_risk_score || 0,
      cognitiveRisk: result.data.aggregated_health_summary.cognitive_risk_score || 0
    } : null

    testResult.value = {
      accountId,
      profileId,
      hasCanaryData,
      showsPending,
      apiSuccess: response.ok,
      riskScores,
      error: response.ok ? null : `HTTP ${response.status}`,
      expectedBehavior: hasCanaryData ? 'Should show real canary data' : 'Should show "Stats Pending"'
    }
    
    console.log('🧪 Test result:', testResult.value)
  } catch (err) {
    console.error('Error testing canary data:', err)
    testResult.value = {
      accountId,
      profileId,
      hasCanaryData: false,
      showsPending: true,
      apiSuccess: false,
      riskScores: null,
      error: err instanceof Error ? err.message : 'Unknown error',
      expectedBehavior: 'Should show "Stats Pending"'
    }
  }
}
</script>
