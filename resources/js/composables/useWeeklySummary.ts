import { ref, computed, onMounted, onUnmounted } from 'vue'
import { weeklySummaryApi, type WeeklySummaryData } from '@/lib/weeklySummaryApi'

export interface UseWeeklySummaryOptions {
  accountId: number
  profileId: number
  timeRangeWeeks?: number
  autoRefresh?: boolean
  refreshInterval?: number
  useMockData?: boolean
}

export function useWeeklySummary(options: UseWeeklySummaryOptions) {
  const {
    accountId,
    profileId,
    timeRangeWeeks = 1,
    autoRefresh = true,
    refreshInterval = 300000, // 5 minutes
    useMockData = false
  } = options

  // State
  const loading = ref(true)
  const error = ref<string | null>(null)
  const weeklyData = ref<WeeklySummaryData | null>(null)
  const lastGenerated = ref<string>('')

  // Computed
  const hasData = computed(() => !!weeklyData.value)
  const isGenerating = computed(() => loading.value)
  const hasError = computed(() => !!error.value)

  // Methods
  const fetchWeeklySummary = async () => {
    try {
      loading.value = true
      error.value = null

      let response
      
      if (useMockData) {
        response = weeklySummaryApi.getMockWeeklySummary(accountId, profileId, timeRangeWeeks)
      } else {
        response = await weeklySummaryApi.getWeeklySummaryCached(accountId, profileId, timeRangeWeeks)
      }

      if (response.success) {
        weeklyData.value = response.data
        lastGenerated.value = response.data.generated_at
      } else {
        throw new Error(response.message || 'Failed to fetch weekly summary')
      }
    } catch (err: any) {
      error.value = err.message || 'Failed to generate weekly summary'
      console.error('Weekly summary error:', err)
    } finally {
      loading.value = false
    }
  }

  const refresh = async () => {
    // Clear cache for this user
    weeklySummaryApi.clearUserCache(accountId, profileId)
    await fetchWeeklySummary()
  }

  const clearError = () => {
    error.value = null
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

  const getWeekStart = () => {
    if (!weeklyData.value) return new Date()
    const now = new Date()
    const daysSinceStart = now.getDay()
    const weekStart = new Date(now)
    weekStart.setDate(now.getDate() - daysSinceStart)
    return weekStart.toISOString()
  }

  const getWeekEnd = () => {
    if (!weeklyData.value) return new Date()
    const now = new Date()
    const daysSinceStart = now.getDay()
    const weekEnd = new Date(now)
    weekEnd.setDate(now.getDate() + (6 - daysSinceStart))
    return weekEnd.toISOString()
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
      refreshTimer = setInterval(fetchWeeklySummary, refreshInterval)
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
    fetchWeeklySummary()
    startAutoRefresh()
  })

  onUnmounted(() => {
    stopAutoRefresh()
  })

  return {
    // State
    loading,
    error,
    weeklyData,
    lastGenerated,
    
    // Computed
    hasData,
    isGenerating,
    hasError,
    
    // Methods
    fetchWeeklySummary,
    refresh,
    clearError,
    formatDate,
    getWeekStart,
    getWeekEnd,
    getTimeAgo,
    
    // Auto-refresh control
    startAutoRefresh,
    stopAutoRefresh
  }
}

export default useWeeklySummary
