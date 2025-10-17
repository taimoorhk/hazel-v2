import { supabase } from './supabaseClient'

export interface StatsFile {
  filename: string
  path: string
  last_modified: string
  size: number
  data: any
}

export interface StatsResponse {
  success: boolean
  message: string
  profile_id: number
  account_id: number
  data: StatsFile[]
  count: number
}

export interface StatsSummary {
  total_files: number
  total_calls: number
  latest_call: {
    filename: string
    timestamp: string
    data: any
  } | null
  summary: Array<{
    filename: string
    timestamp: string
    duration: number | null
    sentiment: string | null
    topics: string[]
    summary: string | null
  }>
}

export interface StatsSummaryResponse {
  success: boolean
  message: string
  profile_id: number
  account_id: number
  data: StatsSummary
}

export interface PathVerificationResponse {
  success: boolean
  message: string
  profile_id: number
  account_id: number
  exists: boolean
}

class StatsApi {
  private async getAuthHeaders() {
    const { data: { session } } = await supabase.auth.getSession()
    if (!session?.access_token) {
      throw new Error('No authentication token available')
    }
    
    return {
      'Authorization': `Bearer ${session.access_token}`,
      'Content-Type': 'application/json'
    }
  }

  async getProfileStats(profileId: number, accountId: number): Promise<StatsResponse> {
    const headers = await this.getAuthHeaders()
    
    const response = await fetch(`/api/stats/profile?profile_id=${profileId}&account_id=${accountId}`, {
      method: 'GET',
      headers
    })

    if (!response.ok) {
      throw new Error(`Failed to fetch profile stats: ${response.statusText}`)
    }

    return await response.json()
  }

  async getStatsSummary(profileId: number, accountId: number): Promise<StatsSummaryResponse> {
    const headers = await this.getAuthHeaders()
    
    const response = await fetch(`/api/stats/profile/summary?profile_id=${profileId}&account_id=${accountId}`, {
      method: 'GET',
      headers
    })

    if (!response.ok) {
      throw new Error(`Failed to fetch stats summary: ${response.statusText}`)
    }

    return await response.json()
  }

  async getElderlyProfileStats(profileId: number, elderlyAccountId: number): Promise<StatsResponse> {
    const headers = await this.getAuthHeaders()
    
    const response = await fetch(`/api/stats/elderly-profile?profile_id=${profileId}&elderly_account_id=${elderlyAccountId}`, {
      method: 'GET',
      headers
    })

    if (!response.ok) {
      throw new Error(`Failed to fetch elderly profile stats: ${response.statusText}`)
    }

    return await response.json()
  }

  async verifyPath(profileId: number, accountId: number): Promise<PathVerificationResponse> {
    const headers = await this.getAuthHeaders()
    
    const response = await fetch(`/api/stats/profile/verify?profile_id=${profileId}&account_id=${accountId}`, {
      method: 'GET',
      headers
    })

    if (!response.ok) {
      throw new Error(`Failed to verify path: ${response.statusText}`)
    }

    return await response.json()
  }

  async getSpecificStatsFile(profileId: number, accountId: number, filename: string): Promise<any> {
    const headers = await this.getAuthHeaders()
    
    const response = await fetch(`/api/stats/profile/file?profile_id=${profileId}&account_id=${accountId}&filename=${filename}`, {
      method: 'GET',
      headers
    })

    if (!response.ok) {
      throw new Error(`Failed to fetch specific stats file: ${response.statusText}`)
    }

    return await response.json()
  }
}

export const statsApi = new StatsApi()
