import axios from 'axios'

export interface FinalHealthData {
  account_id: number
  profile_id: number
  has_data: boolean
  status: string
  last_updated: string
  total_calls: number
  total_duration: number
  last_call: string
  overall_health_score: number
  cognitive_health_score: number
  mental_health_score: number
  physical_health_score: number
  social_health_score: number
  risk_scores: {
    alzheimer_risk: number
    parkinson_risk: number
    depression_risk: number
    anxiety_risk: number
    fall_risk: number
    cognitive_risk: number
  }
  health_trends: {
    sentiment_trend: string
    engagement_trend: string
    mood_stability: string
    clarity_consistency: string
  }
  weekly_patterns: {
    call_frequency: string
    peak_activity_days: string[]
    energy_patterns: string
  }
  health_insights: {
    strengths: string[]
    concerns: string[]
    recommendations: string[]
  }
  data_quality: {
    completeness: number
    reliability: number
    last_analysis: string
  }
}

export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
  source?: string
}

export interface MultipleUsersResponse {
  success: boolean
  message: string
  data: FinalHealthData[]
  total_users: number
  users_with_data: number
}

export interface RealtimeSyncStatus {
  success: boolean
  message: string
  data: {
    sync_status: string
    last_sync: string
    accounts_synced: number
    profiles_synced: number
    total_files: number
  }
  last_updated: string
}

class FinalHealthDataApi {
  private baseUrl = '/api/final-health-data'

  /**
   * Get final health data for a specific user
   */
  async getFinalHealthData(accountId: number, profileId: number): Promise<ApiResponse<FinalHealthData>> {
    try {
      const response = await axios.post(`${this.baseUrl}/get-final-data`, {
        account_id: accountId,
        profile_id: profileId
      })
      return response.data
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch final health data')
    }
  }

  /**
   * Get final health data for multiple users
   */
  async getMultipleUsersFinalData(users: Array<{account_id: number, profile_id: number}>): Promise<MultipleUsersResponse> {
    try {
      const response = await axios.post(`${this.baseUrl}/get-multiple-users`, {
        users
      })
      return response.data
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch multiple users data')
    }
  }

  /**
   * Get real-time sync status
   */
  async getRealtimeSyncStatus(): Promise<RealtimeSyncStatus> {
    try {
      const response = await axios.get(`${this.baseUrl}/realtime-sync-status`)
      return response.data
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch sync status')
    }
  }

  /**
   * Check if user has data available
   */
  async checkUserDataAvailability(accountId: number, profileId: number): Promise<boolean> {
    try {
      const response = await this.getFinalHealthData(accountId, profileId)
      return response.data.has_data
    } catch {
      return false
    }
  }

  /**
   * Get health data with retry logic
   */
  async getFinalHealthDataWithRetry(
    accountId: number, 
    profileId: number, 
    maxRetries: number = 3,
    retryDelay: number = 1000
  ): Promise<ApiResponse<FinalHealthData>> {
    let lastError: Error | null = null
    
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        return await this.getFinalHealthData(accountId, profileId)
      } catch (error: any) {
        lastError = error
        
        if (attempt < maxRetries) {
          await new Promise(resolve => setTimeout(resolve, retryDelay * attempt))
        }
      }
    }
    
    throw lastError || new Error('Failed to fetch data after retries')
  }

  /**
   * Get health data for all users in an account
   */
  async getAccountUsersFinalData(accountId: number, profileIds: number[]): Promise<FinalHealthData[]> {
    const users = profileIds.map(profileId => ({ account_id: accountId, profile_id: profileId }))
    const response = await this.getMultipleUsersFinalData(users)
    return response.data
  }

  /**
   * Get health data with caching
   */
  private cache = new Map<string, { data: FinalHealthData, timestamp: number }>()
  private cacheTimeout = 5 * 60 * 1000 // 5 minutes

  async getFinalHealthDataCached(accountId: number, profileId: number): Promise<ApiResponse<FinalHealthData>> {
    const cacheKey = `${accountId}-${profileId}`
    const cached = this.cache.get(cacheKey)
    
    if (cached && (Date.now() - cached.timestamp) < this.cacheTimeout) {
      return {
        success: true,
        message: 'Data retrieved from cache',
        data: cached.data,
        source: 'cache'
      }
    }

    const response = await this.getFinalHealthData(accountId, profileId)
    
    if (response.success && response.data.has_data) {
      this.cache.set(cacheKey, {
        data: response.data,
        timestamp: Date.now()
      })
    }
    
    return response
  }

  /**
   * Clear cache for a specific user
   */
  clearUserCache(accountId: number, profileId: number): void {
    const cacheKey = `${accountId}-${profileId}`
    this.cache.delete(cacheKey)
  }

  /**
   * Clear all cache
   */
  clearAllCache(): void {
    this.cache.clear()
  }
}

export const finalHealthDataApi = new FinalHealthDataApi()
export default finalHealthDataApi
