<template>
  <div class="elderly-profiles-status-display">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="loading-text">Loading elderly profiles status...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h3 class="error-title">Unable to Load Status</h3>
      <p class="error-message">{{ error }}</p>
    </div>

    <!-- Elderly Profiles Status -->
    <div v-else-if="elderlyProfilesStatus" class="profiles-status-container">
      <h3 class="section-title">Associated Elderly Profiles</h3>
      
      <!-- Profiles with Data -->
      <div v-if="profilesWithData.length > 0" class="profiles-with-data">
        <h4 class="subsection-title">Profiles with Health Data</h4>
        <div class="profiles-grid">
          <div 
            v-for="profile in profilesWithData" 
            :key="profile.profile_id"
            class="profile-card has-data"
          >
            <div class="profile-header">
              <div class="profile-icon">👤</div>
              <h5 class="profile-name">Profile {{ profile.profile_id }}</h5>
              <span class="status-badge success">Has Data</span>
            </div>
            <div class="profile-stats">
              <div class="stat-item">
                <span class="stat-label">Files:</span>
                <span class="stat-value">{{ profile.file_count }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Canary Analysis:</span>
                <span class="stat-value">{{ profile.canary_file_count }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Last Updated:</span>
                <span class="stat-value">{{ formatDate(profile.last_modified) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Profiles without Data -->
      <div v-if="profilesWithoutData.length > 0" class="profiles-without-data">
        <h4 class="subsection-title">Profiles without Health Data</h4>
        <div class="profiles-grid">
          <div 
            v-for="profile in profilesWithoutData" 
            :key="profile.profile_id"
            class="profile-card no-data"
          >
            <div class="profile-header">
              <div class="profile-icon">👤</div>
              <h5 class="profile-name">Profile {{ profile.profile_id }}</h5>
              <span class="status-badge warning">No Results</span>
            </div>
            <div class="no-data-message">
              <div class="no-data-icon">📊</div>
              <p class="no-data-text">No health data available yet</p>
              <p class="no-data-subtext">Data will appear here once health monitoring begins</p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Elderly Profiles -->
      <div v-if="!profilesWithData.length && !profilesWithoutData.length" class="no-profiles">
        <div class="no-profiles-icon">👥</div>
        <h4 class="no-profiles-title">No Elderly Profiles</h4>
        <p class="no-profiles-text">No elderly profiles are associated with this account</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { dataAvailabilityApi, type ComprehensiveElderlyProfilesStatusResponse } from '@/lib/dataAvailabilityApi'

// Props
const props = defineProps<{
  accountId: number
  systemElderlyProfileIds: number[]
}>()

// State
const loading = ref(true)
const error = ref<string | null>(null)
const elderlyProfilesStatus = ref<ComprehensiveElderlyProfilesStatusResponse | null>(null)

// Computed properties
const profilesWithData = computed(() => {
  if (!elderlyProfilesStatus.value) return []
  
  return Object.values(elderlyProfilesStatus.value.data.elderly_profiles_status)
    .filter(profile => profile.status === 'has_data')
})

const profilesWithoutData = computed(() => {
  if (!elderlyProfilesStatus.value) return []
  
  return Object.values(elderlyProfilesStatus.value.data.elderly_profiles_status)
    .filter(profile => profile.status === 'no_data')
})

// Helper functions
const formatDate = (dateString: string | null) => {
  if (!dateString) return 'Never'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  } catch {
    return 'Unknown'
  }
}

// Load elderly profiles status
const loadElderlyProfilesStatus = async () => {
  loading.value = true
  error.value = null
  
  try {
    const result = await dataAvailabilityApi.getComprehensiveElderlyProfilesStatus(
      props.accountId,
      props.systemElderlyProfileIds
    )
    
    elderlyProfilesStatus.value = result
    console.log('📊 Elderly profiles status loaded:', result)
  } catch (err) {
    console.error('Error loading elderly profiles status:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load elderly profiles status'
  } finally {
    loading.value = false
  }
}

// Auto-refresh every 30 seconds
const refreshInterval = ref<number | null>(null)

onMounted(async () => {
  await loadElderlyProfilesStatus()
  
  // Set up auto-refresh
  refreshInterval.value = window.setInterval(() => {
    loadElderlyProfilesStatus()
  }, 30000)
})

// Cleanup on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
})
</script>

<style scoped>
.elderly-profiles-status-display {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text {
  color: #6b7280;
  font-size: 16px;
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
  text-align: center;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
}

.error-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.error-title {
  color: #dc2626;
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 8px;
}

.error-message {
  color: #991b1b;
  font-size: 14px;
}

.section-title {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 24px;
}

.subsection-title {
  font-size: 18px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 16px;
}

.profiles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
}

.profile-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.profile-card.has-data {
  border-left: 4px solid #10b981;
}

.profile-card.no-data {
  border-left: 4px solid #f59e0b;
  background-color: #fffbeb;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.profile-icon {
  font-size: 24px;
}

.profile-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
  flex: 1;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.status-badge.success {
  background-color: #d1fae5;
  color: #065f46;
}

.status-badge.warning {
  background-color: #fef3c7;
  color: #92400e;
}

.profile-stats {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-label {
  font-size: 14px;
  color: #6b7280;
  font-weight: 500;
}

.stat-value {
  font-size: 14px;
  color: #1f2937;
  font-weight: 600;
}

.no-data-message {
  text-align: center;
  padding: 20px 0;
}

.no-data-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.no-data-text {
  font-size: 16px;
  font-weight: 600;
  color: #f59e0b;
  margin-bottom: 8px;
}

.no-data-subtext {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.no-profiles {
  text-align: center;
  padding: 60px 20px;
  background-color: #f9fafb;
  border-radius: 12px;
  border: 2px dashed #d1d5db;
}

.no-profiles-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.no-profiles-title {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.no-profiles-text {
  font-size: 16px;
  color: #6b7280;
  margin: 0;
}
</style>
