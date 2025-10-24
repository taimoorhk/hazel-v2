<template>
  <AppLayout>
    <Head title="Test Account Isolation" />
    
    <div class="min-h-screen bg-gray-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Account Isolation Test</h1>
          <p class="mt-2 text-gray-600">Testing that each account shows its own data, not mtaimoorhas1@gmail.com data</p>
        </div>

        <!-- Current User Info -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Current User Information</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="flex justify-between">
              <span class="font-medium">Detected User:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ detectedUser || 'Detecting...' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">Account ID:</span>
              <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ detectedAccountId || 'Detecting...' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="font-medium">Status:</span>
              <span :class="isCorrectAccount ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                {{ isCorrectAccount ? 'Correct' : 'Incorrect' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Test Different Account/Profile Combinations</h2>
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
              @click="testAccountIsolation" 
              class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
            >
              Test Account Isolation
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
                <span class="font-medium">Test Account ID:</span>
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ testResult.accountId }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Test Profile ID:</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ testResult.profileId }}</span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">API Success:</span>
                <span :class="testResult.apiSuccess ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded">
                  {{ testResult.apiSuccess ? 'Success' : 'Failed' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Has Data:</span>
                <span :class="testResult.hasData ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded">
                  {{ testResult.hasData ? 'Yes' : 'No' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Shows Pending:</span>
                <span :class="testResult.showsPending ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'" class="px-2 py-1 rounded">
                  {{ testResult.showsPending ? 'Yes' : 'No' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="font-medium">Error:</span>
                <span class="text-red-600">{{ testResult.error || 'None' }}</span>
              </div>
            </div>
          </div>

          <!-- Health Risk Assessment Test -->
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
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dashboard Test</h3>
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
            <div><strong>Account 6, Profile -1:</strong> Should show data for mtaimoorhas1@gmail.com</div>
            <div><strong>Account 6, Profile 15:</strong> Should show data for elderly profile under account 6</div>
            <div><strong>Account 7, Profile -1:</strong> Should show data for microassetsmain@gmail.com</div>
            <div><strong>Account 7, Profile 15:</strong> Should show "Pending" if no data available</div>
            <div><strong>Account 8, Profile -1:</strong> Should show "Pending" if no data available</div>
            <div><strong>Account 101, Profile 101:</strong> Should show "Pending" if no data available</div>
          </div>
        </div>

        <!-- Refresh Test Instructions -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
          <h3 class="text-lg font-semibold text-yellow-900 mb-4">Refresh Test Instructions</h3>
          <div class="space-y-2 text-sm text-yellow-800">
            <div>1. <strong>Login as microassetsmain@gmail.com</strong></div>
            <div>2. <strong>Navigate to any elderly profile</strong></div>
            <div>3. <strong>Check that Account ID shows 7, not 6</strong></div>
            <div>4. <strong>Refresh the page</strong></div>
            <div>5. <strong>Verify Account ID still shows 7, not 6</strong></div>
            <div>6. <strong>Check that data is for microassetsmain@gmail.com, not mtaimoorhas1@gmail.com</strong></div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, computed } from 'vue'
import CleanWorkingDashboard from '@/components/CleanWorkingDashboard.vue'
import { useSupabaseUser } from '@/composables/useSupabaseUser'
import { supabase } from '@/lib/supabaseClient'

// State
const selectedAccountId = ref('6')
const selectedProfileId = ref('-1')
const testResult = ref<any>(null)
const detectedUser = ref<string | null>(null)
const detectedAccountId = ref<number | null>(null)

// Get user composable
const { user } = useSupabaseUser()

// Computed properties
const isCorrectAccount = computed(() => {
  if (!detectedUser.value || !detectedAccountId.value) return false
  
  const expectedAccountId = detectedUser.value === 'microassetsmain@gmail.com' ? 7 : 6
  return detectedAccountId.value === expectedAccountId
})

// Methods
const detectCurrentUser = async () => {
  try {
    // Try multiple methods to detect user
    let userEmail = user.value?.email
    
    if (!userEmail) {
      try {
        const { data: { session } } = await supabase.auth.getSession()
        userEmail = session?.user?.email
      } catch (e) {
        console.log('Could not get Supabase session:', e)
      }
    }
    
    if (!userEmail) {
      try {
        const storedData = localStorage.getItem('sb-hazel-auth-token')
        if (storedData) {
          const parsedData = JSON.parse(storedData)
          userEmail = parsedData?.user?.email
        }
      } catch (e) {
        console.log('Could not parse localStorage:', e)
      }
    }
    
    detectedUser.value = userEmail
    
    if (userEmail) {
      const emailToAccountId: Record<string, number> = {
        'mtaimoorhas1@gmail.com': 6,
        'jsahib@gmail.com': 6,
        'microassetsmain@gmail.com': 7,
      }
      detectedAccountId.value = emailToAccountId[userEmail] || 6
    }
  } catch (error) {
    console.error('Error detecting user:', error)
  }
}

const testAccountIsolation = async () => {
  const accountId = parseInt(selectedAccountId.value)
  const profileId = parseInt(selectedProfileId.value)
  
  try {
    console.log('🧪 Testing account isolation for:', { accountId, profileId })
    
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
    
    const hasData = result.success && result.data?.has_data && result.data?.canary_analysis_files?.length > 0
    const showsPending = !hasData
    
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
      hasData,
      showsPending,
      apiSuccess: response.ok,
      riskScores,
      error: response.ok ? null : `HTTP ${response.status}`,
      expectedBehavior: hasData ? 'Should show real data' : 'Should show "Pending"'
    }
    
    console.log('🧪 Test result:', testResult.value)
  } catch (err) {
    console.error('Error testing account isolation:', err)
    testResult.value = {
      accountId,
      profileId,
      hasData: false,
      showsPending: true,
      apiSuccess: false,
      riskScores: null,
      error: err instanceof Error ? err.message : 'Unknown error',
      expectedBehavior: 'Should show "Pending"'
    }
  }
}

onMounted(async () => {
  await detectCurrentUser()
})
</script>
