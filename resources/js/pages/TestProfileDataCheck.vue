<template>
  <AppLayout>
    <Head title="Test Profile Data Check" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Profile Data Check</h1>
          <p class="mt-2 text-gray-600">Checking what profiles have data in DigitalOcean for Account 6</p>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Configuration</h2>
          <div class="flex justify-center">
            <button 
              @click="checkAccountProfiles" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Check Account 6 Profiles
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center p-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
          <span class="ml-2 text-gray-600">Checking profiles...</span>
        </div>

        <!-- Results -->
        <div v-else-if="results" class="space-y-6">
          <!-- Account Profiles -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account 6 Profiles in DigitalOcean</h3>
            <div class="space-y-2">
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="font-medium">Total Profiles Found:</span>
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ results.total_profiles }}</span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="font-medium">Profile IDs:</span>
                <span class="text-sm text-gray-600">{{ results.profile_ids.join(', ') }}</span>
              </div>
            </div>
          </div>

          <!-- Profile 102 Check -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile 102 Specific Check</h3>
            <div class="space-y-2">
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="font-medium">Profile 102 has data:</span>
                <span :class="profile102HasData ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ profile102HasData ? 'YES' : 'NO' }}
                </span>
              </div>
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="font-medium">Profile 102 in DigitalOcean:</span>
                <span :class="profile102InDigitalOcean ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ profile102InDigitalOcean ? 'YES' : 'NO' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Health Risk Assessment Test -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Risk Assessment Test</h3>
            <div class="space-y-2">
              <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                <span class="font-medium">Can fetch health data:</span>
                <span :class="canFetchHealthData ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ canFetchHealthData ? 'YES' : 'NO' }}
                </span>
              </div>
              <div v-if="healthData" class="mt-4">
                <h4 class="font-medium text-gray-900 mb-2">Health Risk Scores:</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div class="flex justify-between">
                    <span>Alzheimer Risk:</span>
                    <span>{{ healthData.alzheimer_risk_score || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Parkinson Risk:</span>
                    <span>{{ healthData.parkinson_risk_score || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Depression Risk:</span>
                    <span>{{ healthData.depression_risk_score || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Anxiety Risk:</span>
                    <span>{{ healthData.anxiety_risk_score || 'N/A' }}</span>
                  </div>
                </div>
              </div>
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
              <h3 class="text-sm font-medium text-red-800">Error checking profiles</h3>
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
const results = ref<any>(null)
const profile102HasData = ref(false)
const profile102InDigitalOcean = ref(false)
const canFetchHealthData = ref(false)
const healthData = ref<any>(null)

// Methods
const checkAccountProfiles = async () => {
  loading.value = true
  error.value = null
  results.value = null
  profile102HasData.value = false
  profile102InDigitalOcean.value = false
  canFetchHealthData.value = false
  healthData.value = null

  try {
    console.log('🔍 Checking account 6 profiles...')
    
    // Check what profiles exist in DigitalOcean for account 6
    const response = await fetch('/api/realtime-sync/account-profiles?account_id=6')
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const result = await response.json()
    
    if (result.success) {
      results.value = result.data
      profile102InDigitalOcean.value = result.data.profile_ids.includes(102)
      
      console.log('📊 Account 6 profiles:', result.data)
      console.log('📊 Profile 102 in DigitalOcean:', profile102InDigitalOcean.value)
      
      // Now check if profile 102 has data
      if (profile102InDigitalOcean.value) {
        await checkProfile102Data()
      } else {
        console.log('❌ Profile 102 not found in DigitalOcean')
        profile102HasData.value = false
        canFetchHealthData.value = false
      }
    } else {
      throw new Error(result.message || 'Failed to get account profiles')
    }
  } catch (err) {
    console.error('Error checking profiles:', err)
    error.value = err instanceof Error ? err.message : 'Failed to check profiles'
  } finally {
    loading.value = false
  }
}

const checkProfile102Data = async () => {
  try {
    console.log('🔍 Checking profile 102 data...')
    
    // Check if profile 102 has data
    const response = await fetch('/api/realtime-sync/profile-data', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        account_id: 6,
        profile_id: 102
      })
    })
    
    if (response.ok) {
      const result = await response.json()
      
      if (result.success && result.data) {
        profile102HasData.value = result.data.has_data || false
        canFetchHealthData.value = result.data.has_data || false
        
        if (result.data.aggregated_health_summary) {
          healthData.value = result.data.aggregated_health_summary
        }
        
        console.log('📊 Profile 102 data:', result.data)
        console.log('📊 Has data:', profile102HasData.value)
        console.log('📊 Health data:', healthData.value)
      } else {
        console.log('❌ Profile 102 has no data')
        profile102HasData.value = false
        canFetchHealthData.value = false
      }
    } else {
      console.log('❌ Profile 102 API error:', response.status)
      profile102HasData.value = false
      canFetchHealthData.value = false
    }
  } catch (err) {
    console.error('Error checking profile 102 data:', err)
    profile102HasData.value = false
    canFetchHealthData.value = false
  }
}
</script>
