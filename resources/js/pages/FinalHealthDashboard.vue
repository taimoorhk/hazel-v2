<template>
  <div class="final-health-dashboard">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Final Health Dashboard</h1>
              <p class="mt-1 text-sm text-gray-600">
                Comprehensive health analytics from 3rd checkpoint data
              </p>
            </div>
            <div class="flex items-center space-x-4">
              <!-- Real-time Sync Status -->
              <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-600">Real-time sync</span>
              </div>
              
              <!-- Refresh Button -->
              <button
                @click="refreshAllData"
                :disabled="loading"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <svg class="w-4 h-4 mr-2" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- User Selection -->
      <div class="mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Select User</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Account ID</label>
              <select
                v-model="selectedAccountId"
                @change="onUserChange"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="6">Account 6 (mtaimoorhas1@gmail.com)</option>
                <option value="7">Account 7 (microassetsmain@gmail.com)</option>
                <option value="8">Account 8 (test@example.com)</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Profile ID</label>
              <select
                v-model="selectedProfileId"
                @change="onUserChange"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="-1">Main Account (-1)</option>
                <option value="15">Elderly Profile (15)</option>
                <option value="16">Elderly Profile (16)</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Final Health Data Cards -->
      <FinalHealthDataCards
        :account-id="selectedAccountId"
        :profile-id="selectedProfileId"
        :auto-refresh="true"
        :refresh-interval="15000"
      />

      <!-- Multiple Users Overview -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Multiple Users Overview</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="user in multipleUsersData"
              :key="`${user.account_id}-${user.profile_id}`"
              class="border border-gray-200 rounded-lg p-4"
            >
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-900">
                  Account {{ user.account_id }}, Profile {{ user.profile_id }}
                </span>
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="user.has_data ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                >
                  {{ user.has_data ? 'Data Available' : 'Weekly Report Pending' }}
                </span>
              </div>
              <div v-if="user.has_data" class="text-sm text-gray-600">
                <p>Health Score: {{ user.overall_health_score }}/10</p>
                <p>Total Calls: {{ user.total_calls }}</p>
                <p>Last Call: {{ formatDate(user.last_call) }}</p>
              </div>
              <div v-else class="text-sm text-gray-500">
                <p>{{ user.message }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Real-time Sync Status -->
      <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">Real-time Sync Status</h2>
          <div v-if="syncStatus" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ syncStatus.data.accounts_synced }}</div>
              <div class="text-sm text-gray-600">Accounts Synced</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ syncStatus.data.profiles_synced }}</div>
              <div class="text-sm text-gray-600">Profiles Synced</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-purple-600">{{ syncStatus.data.total_files }}</div>
              <div class="text-sm text-gray-600">Total Files</div>
            </div>
            <div class="text-center">
              <div class="text-sm font-bold text-gray-600">{{ formatDate(syncStatus.data.last_sync) }}</div>
              <div class="text-sm text-gray-600">Last Sync</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import FinalHealthDataCards from '@/components/FinalHealthDataCards.vue'
import { finalHealthDataApi, type FinalHealthData, type RealtimeSyncStatus } from '@/lib/finalHealthDataApi'

// State
const selectedAccountId = ref(6)
const selectedProfileId = ref(-1)
const loading = ref(false)
const multipleUsersData = ref<FinalHealthData[]>([])
const syncStatus = ref<RealtimeSyncStatus | null>(null)

// Methods
const onUserChange = () => {
  // User selection changed, the FinalHealthDataCards component will automatically update
}

const refreshAllData = async () => {
  loading.value = true
  try {
    // Refresh multiple users data
    await fetchMultipleUsersData()
    
    // Refresh sync status
    await fetchSyncStatus()
  } finally {
    loading.value = false
  }
}

const fetchMultipleUsersData = async () => {
  try {
    const users = [
      { account_id: 6, profile_id: -1 },
      { account_id: 6, profile_id: 15 },
      { account_id: 7, profile_id: -1 }
    ]
    
    const response = await finalHealthDataApi.getMultipleUsersFinalData(users)
    multipleUsersData.value = response.data
  } catch (error) {
    console.error('Failed to fetch multiple users data:', error)
  }
}

const fetchSyncStatus = async () => {
  try {
    const response = await finalHealthDataApi.getRealtimeSyncStatus()
    syncStatus.value = response
  } catch (error) {
    console.error('Failed to fetch sync status:', error)
  }
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Auto-refresh setup
let refreshTimer: NodeJS.Timeout | null = null

const startAutoRefresh = () => {
  refreshTimer = setInterval(async () => {
    await fetchMultipleUsersData()
    await fetchSyncStatus()
  }, 30000) // 30 seconds
}

const stopAutoRefresh = () => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
    refreshTimer = null
  }
}

// Lifecycle
onMounted(async () => {
  await fetchMultipleUsersData()
  await fetchSyncStatus()
  startAutoRefresh()
})

onUnmounted(() => {
  stopAutoRefresh()
})
</script>

<style scoped>
.final-health-dashboard {
  min-height: 100vh;
  background-color: #f3f4f6;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
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
