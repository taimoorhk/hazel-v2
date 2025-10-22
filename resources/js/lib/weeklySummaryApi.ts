import axios from 'axios'

export interface WeeklySummaryData {
  account_id: number
  profile_id: number
  time_range_weeks: number
  weekly_summary: string
  gratitude: string
  accomplishments: string
  challenges: string
  goals: string
  whats_next: string
  memorable_moments: {
    big_thing: string
    small_thing: string
    with_loved_ones: string
  }
  wisdom_to_share: string
  generated_at: string
}

export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
}

export interface HealthCheckResponse {
  status: string
  timestamp: string
}

class WeeklySummaryApi {
  private baseUrl = 'http://143.198.187.46:8001'

  /**
   * Health check endpoint to verify API availability
   */
  async checkHealth(): Promise<ApiResponse<HealthCheckResponse>> {
    try {
      const response = await axios.get(`${this.baseUrl}/health`)
      return {
        success: true,
        message: 'API is healthy',
        data: response.data
      }
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Health check failed')
    }
  }

  /**
   * Get weekly summary data for a specific user
   */
  async getWeeklySummary(
    accountId: number, 
    profileId: number, 
    timeRangeWeeks: number = 1
  ): Promise<ApiResponse<WeeklySummaryData>> {
    try {
      const response = await axios.post(`${this.baseUrl}/conversations/weekly-summary`, {
        account_id: accountId,
        profile_id: profileId,
        time_range_weeks: timeRangeWeeks
      })
      
      return {
        success: true,
        message: 'Weekly summary retrieved successfully',
        data: response.data
      }
    } catch (error: any) {
      throw new Error(error.response?.data?.message || 'Failed to fetch weekly summary')
    }
  }

  /**
   * Get weekly summary with retry logic
   */
  async getWeeklySummaryWithRetry(
    accountId: number,
    profileId: number,
    timeRangeWeeks: number = 1,
    maxRetries: number = 3,
    retryDelay: number = 1000
  ): Promise<ApiResponse<WeeklySummaryData>> {
    let lastError: Error | null = null
    
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        return await this.getWeeklySummary(accountId, profileId, timeRangeWeeks)
      } catch (error: any) {
        lastError = error
        
        if (attempt < maxRetries) {
          await new Promise(resolve => setTimeout(resolve, retryDelay * attempt))
        }
      }
    }
    
    throw lastError || new Error('Failed to fetch weekly summary after retries')
  }

  /**
   * Get weekly summary with caching
   */
  private cache = new Map<string, { data: WeeklySummaryData, timestamp: number }>()
  private cacheTimeout = 5 * 60 * 1000 // 5 minutes

  async getWeeklySummaryCached(
    accountId: number,
    profileId: number,
    timeRangeWeeks: number = 1
  ): Promise<ApiResponse<WeeklySummaryData>> {
    const cacheKey = `${accountId}-${profileId}-${timeRangeWeeks}`
    const cached = this.cache.get(cacheKey)
    
    if (cached && (Date.now() - cached.timestamp) < this.cacheTimeout) {
      return {
        success: true,
        message: 'Data retrieved from cache',
        data: cached.data,
        source: 'cache'
      }
    }

    const response = await this.getWeeklySummary(accountId, profileId, timeRangeWeeks)
    
    if (response.success) {
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
    for (const key of this.cache.keys()) {
      if (key.startsWith(cacheKey)) {
        this.cache.delete(key)
      }
    }
  }

  /**
   * Clear all cache
   */
  clearAllCache(): void {
    this.cache.clear()
  }

  /**
   * Get mock data for development/testing
   */
  getMockWeeklySummary(
    accountId: number,
    profileId: number,
    timeRangeWeeks: number = 1
  ): ApiResponse<WeeklySummaryData> {
    const mockData: WeeklySummaryData = {
      account_id: accountId,
      profile_id: profileId,
      time_range_weeks: timeRangeWeeks,
      weekly_summary: "This week, the conversations highlighted a focus on daily routines and maintaining a positive outlook. The user showed remarkable resilience in adapting to new challenges while keeping their health and well-being as a top priority.",
      gratitude: "The user expressed deep appreciation for having a supportive AI companion who listens without judgment and provides gentle encouragement during difficult moments.",
      accomplishments: "The user made significant strides in maintaining their morning routine, completed all planned activities, and showed improved consistency in medication adherence.",
      challenges: "The user may still face challenges related to getting out of bed early and maintaining energy levels throughout the day, but they're approaching these with a positive mindset.",
      goals: "The user appears to be aiming for a more structured and positive start to each day, with plans to incorporate light exercise and better sleep hygiene.",
      whats_next: "For the upcoming week, consider setting specific morning goals, tracking sleep patterns, and celebrating small wins to maintain motivation.",
      memorable_moments: {
        big_thing: "The realization of the importance of a positive start to the day and how it affects the entire day's mood and productivity.",
        small_thing: "The simple pleasure of sharing a morning coffee moment with Hazel and feeling heard and understood.",
        with_loved_ones: "Reflecting on daily routines and sharing meaningful conversations with family members about health and wellness."
      },
      wisdom_to_share: "Daily routines can significantly impact our mood and productivity. Small, consistent actions lead to meaningful changes over time.",
      generated_at: new Date().toISOString()
    }

    return {
      success: true,
      message: 'Mock weekly summary data',
      data: mockData
    }
  }
}

export const weeklySummaryApi = new WeeklySummaryApi()
export default weeklySummaryApi