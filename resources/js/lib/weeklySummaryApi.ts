interface HealthCheckResponse {
  status: string;
  timestamp: string;
}

interface ConversationDataResponse {
  account_id: number;
  profile_id: number;
  time_range_weeks: number;
  conversations: Array<{
    id: string;
    timestamp: string;
    duration: number;
    sentiment: string;
    topics: string[];
    transcript: string;
  }>;
  total_conversations: number;
  total_duration: number;
  average_sentiment: string;
}

interface WeeklySummaryResponse {
  account_id: number;
  profile_id: number;
  time_range_weeks: number;
  summary: {
    total_conversations: number;
    total_duration: number;
    average_sentiment: string;
    key_topics: string[];
    health_insights: string[];
    weekly_trends: string[];
    recommendations: string[];
  };
}

interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
}

class WeeklySummaryApi {
  private baseUrl = 'http://143.198.187.46:8001';

  async checkHealth(): Promise<ApiResponse<HealthCheckResponse>> {
    try {
      const response = await fetch(`${this.baseUrl}/health`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      return {
        success: true,
        data: data
      };
    } catch (error) {
      console.error('Health check failed:', error);
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Health check failed'
      };
    }
  }

  async getConversationData(accountId: number, profileId: number, timeRangeWeeks: number = 1): Promise<ApiResponse<ConversationDataResponse>> {
    try {
      const response = await fetch(`${this.baseUrl}/conversations/data`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          account_id: accountId,
          profile_id: profileId,
          time_range_weeks: timeRangeWeeks
        })
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      
      // Handle API error responses
      if (data.detail && data.detail.includes('Failed to retrieve')) {
        return {
          success: false,
          error: data.detail
        };
      }

      return {
        success: true,
        data: data
      };
    } catch (error) {
      console.error('Conversation data API error:', error);
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to fetch conversation data'
      };
    }
  }

  async getWeeklySummary(accountId: number, profileId: number, timeRangeWeeks: number = 1): Promise<ApiResponse<WeeklySummaryResponse>> {
    try {
      const response = await fetch(`${this.baseUrl}/conversations/weekly-summary`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          account_id: accountId,
          profile_id: profileId,
          time_range_weeks: timeRangeWeeks
        })
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      
      // Handle API error responses
      if (data.detail && data.detail.includes('Failed to retrieve')) {
        return {
          success: false,
          error: data.detail
        };
      }

      return {
        success: true,
        data: data
      };
    } catch (error) {
      console.error('Weekly summary API error:', error);
      return {
        success: false,
        error: error instanceof Error ? error.message : 'Failed to fetch weekly summary'
      };
    }
  }

  // Combined method to get all data
  async getAllData(accountId: number, profileId: number, timeRangeWeeks: number = 1) {
    const [healthCheck, conversationData, weeklySummary] = await Promise.all([
      this.checkHealth(),
      this.getConversationData(accountId, profileId, timeRangeWeeks),
      this.getWeeklySummary(accountId, profileId, timeRangeWeeks)
    ]);

    return {
      health: healthCheck,
      conversations: conversationData,
      summary: weeklySummary
    };
  }
}

export const weeklySummaryApi = new WeeklySummaryApi();
export type { WeeklySummaryResponse };
