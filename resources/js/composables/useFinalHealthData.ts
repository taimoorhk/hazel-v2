import { ref, computed, onMounted, onUnmounted } from 'vue'
import { finalHealthDataApi, type FinalHealthData, type ApiResponse } from '@/lib/finalHealthDataApi'

export interface UseFinalHealthDataOptions {
  accountId: number
  profileId: number
  autoRefresh?: boolean
  refreshInterval?: number
  enableCache?: boolean
  maxRetries?: number
}

export function useFinalHealthData(options: UseFinalHealthDataOptions) {
  const {
    accountId,
    profileId,
    autoRefresh = true,
    refreshInterval = 15000, // 15 seconds
    enableCache = true,
    maxRetries = 3
  } = options

  // State
  const loading = ref(true)
  const error = ref<string | null>(null)
  const healthData = ref<FinalHealthData | null>(null)
  const hasData = ref(false)
  const lastUpdated = ref<string>('')
  const lastChecked = ref<string>('')
  const retryCount = ref(0)

  // Computed
  const isDataAvailable = computed(() => hasData.value && healthData.value?.has_data)
  const isPending = computed(() => !hasData.value && !error.value)
  const isError = computed(() => !!error.value)
  const overallHealthScore = computed(() => healthData.value?.overall_health_score || 0)
  const riskLevel = computed(() => {
    if (!healthData.value?.risk_scores) return 'Low'
    
    const risks = Object.values(healthData.value.risk_scores)
    const avgRisk = risks.reduce((sum, risk) => sum + risk, 0) / risks.length
    
    if (avgRisk >= 7) return 'High'
    if (avgRisk >= 4) return 'Medium'
    return 'Low'
  })

  // Methods
  const fetchHealthData = async () => {
    try {
      loading.value = true
      error.value = null

      const response = enableCache 
        ? await finalHealthDataApi.getFinalHealthDataCached(accountId, profileId)
        : await finalHealthDataApi.getFinalHealthData(accountId, profileId)

      if (response.success) {
        healthData.value = response.data
        hasData.value = response.data.has_data
        lastUpdated.value = response.data.last_updated || new Date().toISOString()
        lastChecked.value = new Date().toISOString()
        retryCount.value = 0
      } else {
        hasData.value = false
        lastChecked.value = new Date().toISOString()
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch health data'
      hasData.value = false
      lastChecked.value = new Date().toISOString()
      
      // Retry logic
      if (retryCount.value < maxRetries) {
        retryCount.value++
        setTimeout(() => {
          fetchHealthData()
        }, 1000 * retryCount.value)
      }
    } finally {
      loading.value = false
    }
  }

  const refresh = async () => {
    // Clear cache for this user
    finalHealthDataApi.clearUserCache(accountId, profileId)
    await fetchHealthData()
  }

  const clearError = () => {
    error.value = null
    retryCount.value = 0
  }

  const getHealthScoreColor = (score: number) => {
    if (score >= 8) return 'text-green-600'
    if (score >= 6) return 'text-yellow-600'
    return 'text-red-600'
  }

  const getRiskLevelColor = (level: string) => {
    switch (level) {
      case 'High': return 'text-red-600'
      case 'Medium': return 'text-yellow-600'
      default: return 'text-green-600'
    }
  }

  const getTrendColor = (trend: string) => {
    switch (trend?.toLowerCase()) {
      case 'improving': return 'text-green-600'
      case 'declining': return 'text-red-600'
      case 'stable': return 'text-blue-600'
      default: return 'text-gray-600'
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

  const formatDuration = (seconds: number) => {
    if (!seconds) return '0s'
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const secs = seconds % 60
    
    if (hours > 0) {
      return `${hours}h ${minutes}m ${secs}s`
    } else if (minutes > 0) {
      return `${minutes}m ${secs}s`
    } else {
      return `${secs}s`
    }
  }

  const getTimeAgo = (dateString: string) => {
    if (!dateString) return 'N/A'
    const now = new Date()
    const date = new Date(dateString)
    const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60))
    
    if (diffInMinutes < 1) return 'Just now'
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`
    return `${Math.floor(diffInMinutes / 1440)}d ago`
  }

  // Auto-refresh setup
  let refreshTimer: NodeJS.Timeout | null = null

  const startAutoRefresh = () => {
    if (autoRefresh && refreshInterval > 0) {
      refreshTimer = setInterval(fetchHealthData, refreshInterval)
    }
  }

  const stopAutoRefresh = () => {
    if (refreshTimer) {
      clearInterval(refreshTimer)
      refreshTimer = null
    }
  }

  // Lifecycle
  onMounted(() => {
    fetchHealthData()
    startAutoRefresh()
  })

  onUnmounted(() => {
    stopAutoRefresh()
  })

  return {
    // State
    loading,
    error,
    healthData,
    hasData,
    lastUpdated,
    lastChecked,
    retryCount,
    
    // Computed
    isDataAvailable,
    isPending,
    isError,
    overallHealthScore,
    riskLevel,
    
    // Methods
    fetchHealthData,
    refresh,
    clearError,
    getHealthScoreColor,
    getRiskLevelColor,
    getTrendColor,
    formatDate,
    formatDuration,
    getTimeAgo,
    
    // Auto-refresh control
    startAutoRefresh,
    stopAutoRefresh
  }
}

export default useFinalHealthData
