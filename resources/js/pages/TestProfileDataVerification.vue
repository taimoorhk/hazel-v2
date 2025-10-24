<template>
  <AppLayout>
    <Head title="Profile Data Verification" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Profile Data Verification</h1>
          <p class="mt-2 text-gray-600">Verifying Weekly Summary and Health Risk Assessment data for each profile</p>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Configuration</h2>
          <div class="flex justify-center space-x-4">
            <button 
              @click="testAllProfiles" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Test All Profiles
            </button>
            <button 
              @click="clearResults" 
              class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors"
            >
              Clear Results
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center p-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">Testing profiles...</span>
        </div>

        <!-- Results -->
        <div v-else-if="results.length > 0" class="space-y-6">
          <div v-for="result in results" :key="`${result.accountId}-${result.profileId}`" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">
                Account {{ result.accountId }}, Profile {{ result.profileId }}
              </h3>
              <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full" :class="result.hasData ? 'bg-green-400' : 'bg-red-400'"></div>
                <span class="text-sm font-medium" :class="result.hasData ? 'text-green-600' : 'text-red-600'">
                  {{ result.hasData ? 'Has Data' : 'No Data' }}
                </span>
              </div>
            </div>

            <!-- Weekly Summary Status -->
            <div class="mb-4">
              <h4 class="font-medium text-gray-900 mb-2">Weekly Summary</h4>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                  <span>Status:</span>
                  <span :class="result.weeklySummary.hasData ? 'text-green-600' : 'text-red-600'">
                    {{ result.weeklySummary.hasData ? 'Available' : 'Pending' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>API Response:</span>
                  <span :class="result.weeklySummary.apiSuccess ? 'text-green-600' : 'text-red-600'">
                    {{ result.weeklySummary.apiSuccess ? 'Success' : 'Failed' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>Mock Data:</span>
                  <span class="text-gray-600">{{ result.weeklySummary.usingMockData ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Error:</span>
                  <span class="text-red-600">{{ result.weeklySummary.error || 'None' }}</span>
                </div>
              </div>
            </div>

            <!-- Health Risk Assessment Status -->
            <div class="mb-4">
              <h4 class="font-medium text-gray-900 mb-2">Health Risk Assessment</h4>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                  <span>Status:</span>
                  <span :class="result.healthRisk.hasData ? 'text-green-600' : 'text-red-600'">
                    {{ result.healthRisk.hasData ? 'Available' : 'Pending' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>API Response:</span>
                  <span :class="result.healthRisk.apiSuccess ? 'text-green-600' : 'text-red-600'">
                    {{ result.healthRisk.apiSuccess ? 'Success' : 'Failed' }}
                  </span>
                </div>
                <div class="flex justify-between">
                  <span>Error:</span>
                  <span class="text-red-600">{{ result.healthRisk.error || 'None' }}</span>
                </div>
              </div>
            </div>

            <!-- Risk Scores -->
            <div v-if="result.healthRisk.hasData" class="mb-4">
              <h4 class="font-medium text-gray-900 mb-2">Risk Scores</h4>
              <div class="grid grid-cols-3 gap-4 text-sm">
                <div class="flex justify-between">
                  <span>Alzheimer:</span>
                  <span class="font-medium">{{ result.healthRisk.alzheimerRisk || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Parkinson:</span>
                  <span class="font-medium">{{ result.healthRisk.parkinsonRisk || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Depression:</span>
                  <span class="font-medium">{{ result.healthRisk.depressionRisk || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Anxiety:</span>
                  <span class="font-medium">{{ result.healthRisk.anxietyRisk || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Fall Risk:</span>
                  <span class="font-medium">{{ result.healthRisk.fallRisk || 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Cognitive:</span>
                  <span class="font-medium">{{ result.healthRisk.cognitiveRisk || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <!-- Raw Data (for debugging) -->
            <div class="mt-4">
              <details class="text-sm">
                <summary class="cursor-pointer font-medium text-gray-700 hover:text-gray-900">Raw Data (Click to expand)</summary>
                <pre class="mt-2 p-3 bg-gray-100 rounded text-xs overflow-auto max-h-40">{{ JSON.stringify(result, null, 2) }}</pre>
              </details>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error testing profiles</h3>
              <p class="mt-1 text-sm text-red-700">{{ error }}</p>
            </div>
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

// State
const loading = ref(false)
const error = ref<string | null>(null)
const results = ref<any[]>([])

// Test profiles configuration
const testProfiles = [
  { accountId: 6, profileId: -1, name: 'Main Account (mtaimoorhas1@gmail.com)' },
  { accountId: 6, profileId: 15, name: 'Elderly Profile 15 (jsahib@gmail.com)' },
  { accountId: 6, profileId: 102, name: 'Elderly Profile 102 (Taimoor)' },
  { accountId: 7, profileId: -1, name: 'Account 7 (microassetsmain@gmail.com)' },
  { accountId: 7, profileId: 15, name: 'Account 7 Elderly Profile 15' },
  { accountId: 101, profileId: 101, name: 'Test Account 101' }
]

// Methods
const testAllProfiles = async () => {
  loading.value = true
  error.value = null
  results.value = []

  try {
    console.log('🔍 Testing all profiles...')
    
    for (const profile of testProfiles) {
      console.log(`Testing Account ${profile.accountId}, Profile ${profile.profileId}`)
      
      const result = {
        accountId: profile.accountId,
        profileId: profile.profileId,
        name: profile.name,
        hasData: false,
        weeklySummary: {
          hasData: false,
          apiSuccess: false,
          usingMockData: false,
          error: null
        },
        healthRisk: {
          hasData: false,
          apiSuccess: false,
          error: null,
          alzheimerRisk: null,
          parkinsonRisk: null,
          depressionRisk: null,
          anxietyRisk: null,
          fallRisk: null,
          cognitiveRisk: null
        }
      }

      // Test Weekly Summary
      try {
        const weeklyResponse = await fetch('/api/realtime-sync/profile-data', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            account_id: profile.accountId,
            profile_id: profile.profileId
          })
        })

        if (weeklyResponse.ok) {
          const weeklyData = await weeklyResponse.json()
          result.weeklySummary.apiSuccess = true
          result.weeklySummary.hasData = weeklyData.success && weeklyData.data?.has_data
          result.weeklySummary.usingMockData = !result.weeklySummary.hasData
        } else {
          result.weeklySummary.error = `HTTP ${weeklyResponse.status}`
        }
      } catch (err) {
        result.weeklySummary.error = err instanceof Error ? err.message : 'Unknown error'
      }

      // Test Health Risk Assessment
      try {
        const healthResponse = await fetch('/api/realtime-sync/profile-data', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            account_id: profile.accountId,
            profile_id: profile.profileId
          })
        })

        if (healthResponse.ok) {
          const healthData = await healthResponse.json()
          result.healthRisk.apiSuccess = true
          result.healthRisk.hasData = healthData.success && healthData.data?.has_data
          
          if (result.healthRisk.hasData && healthData.data?.aggregated_health_summary) {
            const summary = healthData.data.aggregated_health_summary
            result.healthRisk.alzheimerRisk = summary.alzheimer_risk_score || 0
            result.healthRisk.parkinsonRisk = summary.parkinson_risk_score || 0
            result.healthRisk.depressionRisk = summary.depression_risk_score || 0
            result.healthRisk.anxietyRisk = summary.anxiety_risk_score || 0
            result.healthRisk.fallRisk = summary.fall_risk_score || 0
            result.healthRisk.cognitiveRisk = summary.cognitive_risk_score || 0
          }
        } else {
          result.healthRisk.error = `HTTP ${healthResponse.status}`
        }
      } catch (err) {
        result.healthRisk.error = err instanceof Error ? err.message : 'Unknown error'
      }

      // Determine overall data availability
      result.hasData = result.weeklySummary.hasData || result.healthRisk.hasData

      results.value.push(result)
      
      // Small delay between requests
      await new Promise(resolve => setTimeout(resolve, 500))
    }

    console.log('✅ All profiles tested:', results.value)
  } catch (err) {
    console.error('Error testing profiles:', err)
    error.value = err instanceof Error ? err.message : 'Failed to test profiles'
  } finally {
    loading.value = false
  }
}

const clearResults = () => {
  results.value = []
  error.value = null
}
</script>
