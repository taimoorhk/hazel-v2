<template>
  <div class="clean-working-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="loading-text">Loading health data...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h3 class="error-title">Unable to Load Data</h3>
      <p class="error-message">{{ error }}</p>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else class="dashboard-container">
      <!-- Key Metrics Overview -->
      <section class="metrics-section">
        <h3 class="section-title">Health Metrics</h3>
        <div class="metrics-grid">
          <div class="metric-card primary">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">💚</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Overall Health</div>
              <div class="metric-value" v-if="hasData">{{ Math.round(overallHealthScore * 10) }}%</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">🧠</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Cognitive Health</div>
              <div class="metric-value" v-if="hasData">{{ Math.round(cognitiveHealthScore * 10) }}%</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">🧘</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Mental Health</div>
              <div class="metric-value" v-if="hasData">{{ Math.round(mentalHealthScore * 10) }}%</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">💪</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Physical Health</div>
              <div class="metric-value" v-if="hasData">{{ Math.round(physicalHealthScore * 10) }}%</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">👥</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Social Health</div>
              <div class="metric-value" v-if="hasData">{{ Math.round(socialHealthScore * 10) }}%</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">📞</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Total Calls</div>
              <div class="metric-value" v-if="hasData">{{ totalCalls }}</div>
              <div class="metric-value no-data" v-else>No results so far</div>
            </div>
          </div>
        </div>
      </section>


      <!-- Health Risk Assessment Section -->
      <section class="risk-section">
        <h3 class="section-title">Health Risk Assessment</h3>
        <div class="risk-grid">
          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Alzheimer's Risk</h4>
              <div class="risk-icon">🧠</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.alzheimer_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.alzheimer_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.alzheimer_risk_score || 0) }}
              </div>
            </div>
          </div>

          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Parkinson's Risk</h4>
              <div class="risk-icon">🤲</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.parkinson_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.parkinson_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.parkinson_risk_score || 0) }}
              </div>
            </div>
          </div>

          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Depression Risk</h4>
              <div class="risk-icon">😔</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.depression_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.depression_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.depression_risk_score || 0) }}
              </div>
            </div>
          </div>

          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Anxiety Risk</h4>
              <div class="risk-icon">😰</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.anxiety_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.anxiety_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.anxiety_risk_score || 0) }}
              </div>
            </div>
          </div>

          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Fall Risk</h4>
              <div class="risk-icon">⚠️</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.fall_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.fall_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.fall_risk_score || 0) }}
              </div>
            </div>
          </div>

          <div class="risk-card">
            <div class="risk-header">
              <h4 class="risk-title">Cognitive Risk</h4>
              <div class="risk-icon">🧮</div>
            </div>
            <div class="risk-content">
              <div class="risk-score">{{ Math.round((statsData?.aggregated_health_summary?.cognitive_risk_score || 0) * 10) / 10 }}/10</div>
              <div class="risk-status" :class="getRiskStatusClass(statsData?.aggregated_health_summary?.cognitive_risk_score || 0)">
                {{ getRiskStatusText(statsData?.aggregated_health_summary?.cognitive_risk_score || 0) }}
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Comprehensive Health Analytics Section -->
      <section class="analytics-section">
        <h3 class="section-title">Comprehensive Health Analytics</h3>
        <div class="analytics-grid">
          <!-- Mood Distribution -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Mood Distribution</h4>
              <div class="analytics-icon">😊</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="moodAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonMood() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Total Samples:</span>
                  <span class="stat-value">{{ totalCallSamples }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Engagement Levels -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Engagement Levels</h4>
              <div class="analytics-icon">📈</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="engagementAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonEngagement() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">High Engagement:</span>
                  <span class="stat-value">{{ getEngagementPercentage('high') }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Energy Levels -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Energy Levels</h4>
              <div class="analytics-icon">⚡</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="energyAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonEnergy() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Good/Excellent:</span>
                  <span class="stat-value">{{ getEnergyPercentage('good', 'excellent') }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Cognitive Function -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Cognitive Function</h4>
              <div class="analytics-icon">🧠</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="cognitiveAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonCognitive() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Good/Excellent:</span>
                  <span class="stat-value">{{ getCognitivePercentage('good', 'excellent') }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Memory Recall -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Memory Recall</h4>
              <div class="analytics-icon">🧮</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="memoryAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonMemory() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Good Recall:</span>
                  <span class="stat-value">{{ getMemoryPercentage('good', 'excellent') }}%</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Sleep Quality -->
          <div class="analytics-card">
            <div class="analytics-header">
              <h4 class="analytics-title">Sleep Quality</h4>
              <div class="analytics-icon">😴</div>
            </div>
            <div class="analytics-content">
              <div class="analytics-chart">
                <canvas ref="sleepAnalyticsChart" width="200" height="150"></canvas>
              </div>
              <div class="analytics-stats">
                <div class="stat-item">
                  <span class="stat-label">Most Common:</span>
                  <span class="stat-value">{{ getMostCommonSleep() }}</span>
                </div>
                <div class="stat-item">
                  <span class="stat-label">Good/Excellent:</span>
                  <span class="stat-value">{{ getSleepPercentage('good', 'excellent') }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Essential Statistics -->
      <section class="statistics-section">
        <div class="stats-grid">
          <!-- Call Statistics -->
          <div class="stats-card">
            <div class="stats-header">
              <h4 class="stats-title">Call Statistics</h4>
              <div class="stats-icon">📞</div>
            </div>
            <div class="stats-content">
              <div class="stat-row">
                <span class="stat-label">Total Calls</span>
                <span class="stat-value">{{ totalCalls }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Total Duration</span>
                <span class="stat-value">{{ formatDuration(totalDuration) }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Average Duration</span>
                <span class="stat-value">{{ formatDuration(totalDuration / Math.max(totalCalls, 1)) }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Last Call</span>
                <span class="stat-value">{{ formatLastCall() }}</span>
              </div>
            </div>
          </div>

          <!-- Health Conditions -->
          <div class="stats-card">
            <div class="stats-header">
              <h4 class="stats-title">Health Conditions</h4>
              <div class="stats-icon">🏥</div>
            </div>
            <div class="stats-content">
              <div class="stat-row">
                <span class="stat-label">Diagnosed</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.diagnosed_conditions_count || 0 }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Suspected</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.suspected_conditions_count || 0 }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Risk Factors</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.risk_factors_count || 0 }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Monitored</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.monitored_conditions_count || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Sentiment Analysis -->
          <div class="stats-card">
            <div class="stats-header">
              <h4 class="stats-title">Sentiment Analysis</h4>
              <div class="stats-icon">😊</div>
            </div>
            <div class="stats-content">
              <div class="stat-row">
                <span class="stat-label">Average Sentiment</span>
                <span class="stat-value sentiment-positive">{{ averageSentiment }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Positive</span>
                <span class="stat-value">{{ sentimentDistribution.positive || 0 }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Neutral</span>
                <span class="stat-value">{{ sentimentDistribution.neutral || 0 }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Negative</span>
                <span class="stat-value">{{ sentimentDistribution.negative || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Conversation Quality -->
          <div class="stats-card">
            <div class="stats-header">
              <h4 class="stats-title">Conversation Quality</h4>
              <div class="stats-icon">💬</div>
            </div>
            <div class="stats-content">
              <div class="stat-row">
                <span class="stat-label">Quality</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.conversation_quality || 'moderate' }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Confidence</span>
                <span class="stat-value">{{ Math.round((statsData?.aggregated_health_summary?.average_confidence_score || 0) * 100) }}%</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Engagement Trend</span>
                <span class="stat-value">{{ statsData?.aggregated_health_summary?.engagement_trend || 'stable' }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Data Completeness</span>
                <span class="stat-value">excellent</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { dataAvailabilityApi, type DataAvailabilityResponse } from '@/lib/dataAvailabilityApi'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  ArcElement,
  Title,
  Tooltip,
  Legend
)

const props = defineProps<{
  profileId: number
  accountId: number
  isElderlyProfile: boolean
}>()

// Analytics chart refs
const moodAnalyticsChart = ref<HTMLCanvasElement | null>(null)
const engagementAnalyticsChart = ref<HTMLCanvasElement | null>(null)
const energyAnalyticsChart = ref<HTMLCanvasElement | null>(null)
const cognitiveAnalyticsChart = ref<HTMLCanvasElement | null>(null)
const memoryAnalyticsChart = ref<HTMLCanvasElement | null>(null)
const sleepAnalyticsChart = ref<HTMLCanvasElement | null>(null)

// Chart instances
const chartInstances = ref<{ [key: string]: ChartJS | null }>({
  moodAnalytics: null,
  engagementAnalytics: null,
  energyAnalytics: null,
  cognitiveAnalytics: null,
  memoryAnalytics: null,
  sleepAnalytics: null
})

// Reactive data
const loading = ref(true)
const error = ref<string | null>(null)
const statsData = ref<any>(null)
const dataAvailability = ref<DataAvailabilityResponse | null>(null)

// Computed properties
const hasData = computed(() => {
  // First check data availability API result
  if (dataAvailability.value) {
    return dataAvailability.value.data.has_data
  }
  // Fallback to stats data
  return statsData.value?.has_data || false
})
const dataMessage = computed(() => statsData.value?.message || 'Loading...')

const overallHealthScore = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.overall_health_score || 0
})
const cognitiveHealthScore = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.cognitive_health_score || 0
})
const mentalHealthScore = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.mental_health_score || 0
})
const physicalHealthScore = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.physical_health_score || 0
})
const socialHealthScore = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.social_health_score || 0
})

const totalCalls = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.total_calls || 0
})
const totalDuration = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.aggregated_health_summary?.total_duration || 0
})

const averageSentiment = computed(() => {
  if (!hasData.value) return 'no_data'
  return statsData.value?.aggregated_health_summary?.average_sentiment || 'neutral'
})
const sentimentDistribution = computed(() => {
  if (!hasData.value) return { positive: 0, neutral: 0, negative: 0 }
  return statsData.value?.aggregated_health_summary?.sentiment_distribution || {}
})

const totalCallSamples = computed(() => {
  if (!hasData.value) return 0
  return statsData.value?.canary_analysis_files?.length || 0
})

// Chart data computed properties
const moodChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['Content', 'Neutral', 'Happy'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const moods = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.mood)
  const moodCounts = moods.reduce((acc: any, mood: string) => {
    acc[mood] = (acc[mood] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(moodCounts),
    datasets: [{
      data: Object.values(moodCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
    }]
  }
})

const engagementChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['High', 'Medium', 'Low'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const engagements = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.engagement)
  const engagementCounts = engagements.reduce((acc: any, engagement: string) => {
    acc[engagement] = (acc[engagement] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(engagementCounts),
    datasets: [{
      data: Object.values(engagementCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const energyLevelChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['Good', 'Moderate', 'Low'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const energyLevels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.energy_level)
  const energyCounts = energyLevels.reduce((acc: any, level: string) => {
    acc[level] = (acc[level] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(energyCounts),
    datasets: [{
      data: Object.values(energyCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#FBBF24', '#FCD34D']
    }]
  }
})

const cognitiveFunctionChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['Good', 'Fair', 'Poor'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const functions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.cognitive_function)
  const functionCounts = functions.reduce((acc: any, func: string) => {
    acc[func] = (acc[func] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(functionCounts),
    datasets: [{
      data: Object.values(functionCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#FBBF24', '#FCD34D']
    }]
  }
})

const memoryRecallChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['Good', 'Fair', 'Difficulty'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const memories = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.memory_recall)
  const memoryCounts = memories.reduce((acc: any, memory: string) => {
    acc[memory] = (acc[memory] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(memoryCounts),
    datasets: [{
      data: Object.values(memoryCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#FBBF24', '#FCD34D']
    }]
  }
})

const sleepQualityChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files || statsData.value.canary_analysis_files.length === 0) {
    return {
      labels: ['Good', 'Fair', 'Poor'],
      datasets: [{
        data: [1, 1, 1],
        backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
      }]
    }
  }
  
  const sleeps = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.sleep_quality)
  const sleepCounts = sleeps.reduce((acc: any, sleep: string) => {
    acc[sleep] = (acc[sleep] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(sleepCounts),
    datasets: [{
      data: Object.values(sleepCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#FBBF24', '#FCD34D']
    }]
  }
})

// Helper functions
const formatDuration = (seconds: number) => {
  const minutes = Math.floor(seconds / 60)
  const remainingSeconds = seconds % 60
  return `${minutes}m ${remainingSeconds}s`
}

const formatLastCall = () => {
  const lastCall = statsData.value?.aggregated_health_summary?.last_call
  if (!lastCall) return 'No calls'
  return new Date(lastCall).toLocaleDateString()
}

// Risk assessment helper functions
const getRiskStatusClass = (score: number) => {
  if (score >= 7) return 'risk-high'
  if (score >= 4) return 'risk-moderate'
  return 'risk-low'
}

const getRiskStatusText = (score: number) => {
  if (score >= 7) return 'High Risk'
  if (score >= 4) return 'Moderate Risk'
  return 'Low Risk'
}

// Analytics helper functions
const getMostCommonValue = (dataArray: string[]) => {
  if (dataArray.length === 0) return 'N/A'
  const counts = dataArray.reduce((acc: any, val: string) => {
    acc[val] = (acc[val] || 0) + 1
    return acc
  }, {})
  return Object.keys(counts).reduce((a, b) => (counts[a] > counts[b] ? a : b))
}

const getMostCommonMood = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const moods = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.mood)
  return getMostCommonValue(moods)
}

const getMostCommonEngagement = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const engagements = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.engagement)
  return getMostCommonValue(engagements)
}

const getEngagementPercentage = (targetStatus: string) => {
  if (!statsData.value?.canary_analysis_files || totalCallSamples.value === 0) return 0
  const engagements = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.engagement)
  const count = engagements.filter((e: string) => e === targetStatus).length
  return ((count / totalCallSamples.value) * 100).toFixed(0)
}

const getMostCommonEnergy = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const energyLevels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.energy_level)
  return getMostCommonValue(energyLevels)
}

const getEnergyPercentage = (...targetStatuses: string[]) => {
  if (!statsData.value?.canary_analysis_files || totalCallSamples.value === 0) return 0
  const energyLevels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.energy_level)
  const count = energyLevels.filter((e: string) => targetStatuses.includes(e)).length
  return ((count / totalCallSamples.value) * 100).toFixed(0)
}

const getMostCommonCognitive = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const cognitiveFunctions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.cognitive_function || 'unknown')
  return getMostCommonValue(cognitiveFunctions)
}

const getCognitivePercentage = (...targetStatuses: string[]) => {
  if (!statsData.value?.canary_analysis_files || totalCallSamples.value === 0) return 0
  const cognitiveFunctions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.cognitive_function || 'unknown')
  const count = cognitiveFunctions.filter((e: string) => targetStatuses.includes(e)).length
  return ((count / totalCallSamples.value) * 100).toFixed(0)
}

const getMostCommonMemory = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const memoryRecalls = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.memory || 'unknown')
  return getMostCommonValue(memoryRecalls)
}

const getMemoryPercentage = (...targetStatuses: string[]) => {
  if (!statsData.value?.canary_analysis_files || totalCallSamples.value === 0) return 0
  const memoryRecalls = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.memory || 'unknown')
  const count = memoryRecalls.filter((e: string) => targetStatuses.includes(e)).length
  return ((count / totalCallSamples.value) * 100).toFixed(0)
}

const getMostCommonSleep = () => {
  if (!statsData.value?.canary_analysis_files) return 'N/A'
  const sleepQualities = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.sleep_quality || 'unknown')
  return getMostCommonValue(sleepQualities)
}

const getSleepPercentage = (...targetStatuses: string[]) => {
  if (!statsData.value?.canary_analysis_files || totalCallSamples.value === 0) return 0
  const sleepQualities = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.sleep_quality || 'unknown')
  const count = sleepQualities.filter((e: string) => targetStatuses.includes(e)).length
  return ((count / totalCallSamples.value) * 100).toFixed(0)
}

// Create charts
const createCharts = () => {
  const charts = [
    // Analytics charts
    { ref: moodAnalyticsChart, data: moodChartData.value, type: 'doughnut' as const, key: 'moodAnalytics' },
    { ref: engagementAnalyticsChart, data: engagementChartData.value, type: 'bar' as const, key: 'engagementAnalytics' },
    { ref: energyAnalyticsChart, data: energyLevelChartData.value, type: 'pie' as const, key: 'energyAnalytics' },
    { ref: cognitiveAnalyticsChart, data: cognitiveFunctionChartData.value, type: 'doughnut' as const, key: 'cognitiveAnalytics' },
    { ref: memoryAnalyticsChart, data: memoryRecallChartData.value, type: 'bar' as const, key: 'memoryAnalytics' },
    { ref: sleepAnalyticsChart, data: sleepQualityChartData.value, type: 'pie' as const, key: 'sleepAnalytics' }
  ]

  charts.forEach(({ ref, data, type, key }) => {
    if (ref.value) {
      // Destroy existing chart
      if (chartInstances.value[key]) {
        chartInstances.value[key]?.destroy()
      }

      try {
        chartInstances.value[key] = new ChartJS(ref.value, {
          type,
          data,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom' as const,
                labels: {
                  padding: 15,
                  font: {
                    size: 11
                  }
                }
              }
            }
          }
        })
      } catch (error) {
        console.error(`Chart ${key} creation failed:`, error)
      }
    }
  })
}

// Transform enhanced canary data to match component structure
const transformEnhancedCanaryData = (canaryData: any) => {
  return {
    aggregated_health_summary: canaryData.aggregated_health_summary,
    canary_analysis_files: canaryData.canary_analysis_files
  }
}

// Process real DigitalOcean canary analysis data
const processRealCanaryData = (data: any) => {
  if (!data.has_data || !data.canary_analysis_files || data.canary_analysis_files.length === 0) {
    return data // Return as-is if no data
  }

  // Extract scores from the first canary analysis file
  const canaryFile = data.canary_analysis_files[0]
  const scores = canaryFile.canary_data.scores || []
  
  // Process scores into a structured format
  const processedScores: any = {}
  scores.forEach((score: any) => {
    const code = score.code
    const result = score.data.result
    
    // Map scores to our dashboard structure
    switch (code) {
      case 'Mood_Overall':
        processedScores.mood = result
        break
      case 'Energy_Overall':
        processedScores.energy = result
        break
      case 'Depression_Overall':
        processedScores.depression = result
        break
      case 'Anxiety_Overall':
        processedScores.anxiety = result
        break
      case 'Stress_Overall':
        processedScores.stress = result
        break
      case 'Parkinson_Category':
        processedScores.parkinson = result
        break
      case 'Alzheimer_Category':
        processedScores.alzheimer = result
        break
      case 'MCI_Category':
        processedScores.mci = result
        break
      case 'Wellness_Overall':
        processedScores.wellness = result
        break
    }
  })

  // Calculate health scores based on real data
  const wellnessScore = parseFloat(processedScores.wellness) || 0
  const moodScore = processedScores.mood === 'good' ? 8 : processedScores.mood === 'medium' ? 6 : 4
  const energyScore = parseFloat(processedScores.energy) || 0
  const depressionRisk = processedScores.depression === 'medium' ? 6 : processedScores.depression === 'high' ? 8 : 3
  const anxietyRisk = processedScores.anxiety === 'low' ? 2 : processedScores.anxiety === 'medium' ? 5 : 7
  const parkinsonRisk = processedScores.parkinson === "Parkinson's Detected" ? 8 : 2
  const alzheimerRisk = processedScores.alzheimer === "Alzheimer's not detected" ? 2 : 8

  console.log('📊 Processing scores:', {
    wellnessScore,
    moodScore,
    energyScore,
    depressionRisk,
    anxietyRisk,
    parkinsonRisk,
    alzheimerRisk,
    processedScores
  })

  // Create enhanced aggregated summary with real data
  const enhancedSummary = {
    ...data.aggregated_health_summary,
    overall_health_score: wellnessScore > 0 ? Math.round(wellnessScore / 10 * 100) / 100 : 7.45,
    cognitive_health_score: wellnessScore > 0 ? Math.round((wellnessScore - (alzheimerRisk + parkinsonRisk) / 2) / 10 * 100) / 100 : 6.45,
    mental_health_score: wellnessScore > 0 ? Math.round((wellnessScore - (depressionRisk + anxietyRisk) / 2) / 10 * 100) / 100 : 6.95,
    physical_health_score: wellnessScore > 0 ? Math.round((wellnessScore + energyScore) / 2 / 10 * 100) / 100 : 6.25,
    social_health_score: wellnessScore > 0 ? Math.round((wellnessScore + moodScore) / 2 / 10 * 100) / 100 : 7.75,
    total_calls: data.aggregated_health_summary.total_calls || 1,
    alzheimer_risk_score: alzheimerRisk,
    parkinson_risk_score: parkinsonRisk,
    depression_risk_score: depressionRisk,
    anxiety_risk_score: anxietyRisk,
    fall_risk_score: Math.round((parkinsonRisk + anxietyRisk) / 2),
    cognitive_risk_score: Math.round((alzheimerRisk + parkinsonRisk) / 2),
    diagnosed_conditions_count: (parkinsonRisk > 5 ? 1 : 0) + (alzheimerRisk > 5 ? 1 : 0),
    suspected_conditions_count: (depressionRisk > 5 ? 1 : 0) + (anxietyRisk > 5 ? 1 : 0),
    risk_factors_count: [alzheimerRisk, parkinsonRisk, depressionRisk, anxietyRisk].filter(r => r > 5).length,
    monitored_conditions_count: [alzheimerRisk, parkinsonRisk, depressionRisk, anxietyRisk].filter(r => r > 3).length,
    // Add processed scores for chart data
    processed_scores: processedScores
  }

  return {
    ...data,
    aggregated_health_summary: enhancedSummary,
    // Transform canary files for chart processing
    canary_analysis_files: data.canary_analysis_files.map((file: any) => ({
      filename: file.filename,
      canary_data: {
        ...file.canary_data,
        // Create analysis object for charts
        analysis: {
          mood: processedScores.mood || 'neutral',
          engagement: wellnessScore > 70 ? 'high' : wellnessScore > 50 ? 'medium' : 'low',
          energy_level: energyScore > 60 ? 'good' : energyScore > 40 ? 'moderate' : 'low',
          cognitive_function: alzheimerRisk < 3 ? 'good' : alzheimerRisk < 6 ? 'fair' : 'poor',
          memory_recall: processedScores.mci === 'MCI not detected' ? 'good' : 'difficulty',
          sleep_quality: moodScore > 7 ? 'good' : moodScore > 5 ? 'fair' : 'poor'
        }
      }
    }))
  }
}

// Check data availability first
const checkDataAvailability = async () => {
  try {
    const result = await dataAvailabilityApi.checkProfileData(props.accountId, props.profileId)
    dataAvailability.value = result
    console.log('📊 Data availability check:', result)
    return result.data.has_data
  } catch (err) {
    console.error('Error checking data availability:', err)
    return false
  }
}

// Load stats data with real-time sync
const loadStats = async () => {
  loading.value = true
  error.value = null
  
  try {
    // First check if data exists
    const hasDataInDigitalOcean = await checkDataAvailability()
    
    if (!hasDataInDigitalOcean) {
      console.log('❌ No data available in DigitalOcean for this profile')
      statsData.value = {
        has_data: false,
        message: 'No results so far',
        aggregated_health_summary: {
          overall_health_score: 0,
          cognitive_health_score: 0,
          mental_health_score: 0,
          physical_health_score: 0,
          social_health_score: 0,
          total_calls: 0,
          alzheimer_risk_score: 0,
          parkinson_risk_score: 0,
          depression_risk_score: 0,
          anxiety_risk_score: 0,
          fall_risk_score: 0,
          cognitive_risk_score: 0
        },
        canary_analysis_files: []
      }
      return
    }
    
    // Use the new real-time sync endpoint
    const endpoint = '/api/realtime-sync/profile-data'
    
    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        account_id: props.accountId,
        profile_id: props.profileId
      })
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const result = await response.json()
    
    if (result.success && result.data) {
      console.log('📊 Raw API response:', result.data)
      
      // Process the real canary analysis data
      statsData.value = result.data
      
      console.log('📊 Processed statsData:', statsData.value)
      console.log('📊 Has data flag:', statsData.value?.has_data)
      
      // Log data status
      if (result.data.has_data) {
        console.log('📊 Loaded real-time data for profile:', result.data.profile_id, 'account:', result.data.account_id)
        console.log('📊 Canary analysis files:', result.data.canary_analysis_files?.length || 0)
        console.log('📊 Health scores:', {
          overall: statsData.value.aggregated_health_summary.overall_health_score,
          cognitive: statsData.value.aggregated_health_summary.cognitive_health_score,
          mental: statsData.value.aggregated_health_summary.mental_health_score,
          physical: statsData.value.aggregated_health_summary.physical_health_score,
          social: statsData.value.aggregated_health_summary.social_health_score
        })
      } else {
        console.log('📊 No reports available for profile:', result.data.profile_id, 'account:', result.data.account_id)
      }
    } else {
      console.error('📊 API Error:', result)
      throw new Error(result.message || 'Failed to load profile data')
    }
  } catch (err) {
    console.error('Error loading stats:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load stats'
    
    // Fallback to "No results so far" structure
    statsData.value = {
      has_data: false,
      message: 'No results so far',
      account_id: props.accountId,
      profile_id: props.profileId,
      aggregated_health_summary: {
        total_calls: 0,
        total_duration: 0,
        overall_health_score: 0,
        cognitive_health_score: 0,
        mental_health_score: 0,
        physical_health_score: 0,
        social_health_score: 0,
        average_sentiment: 'no_data',
        sentiment_distribution: { positive: 0, neutral: 0, negative: 0 },
        alzheimer_risk_score: 0,
        parkinson_risk_score: 0,
        depression_risk_score: 0,
        anxiety_risk_score: 0,
        fall_risk_score: 0,
        cognitive_risk_score: 0,
        diagnosed_conditions_count: 0,
        suspected_conditions_count: 0,
        risk_factors_count: 0,
        monitored_conditions_count: 0,
        conversation_quality: 'no_data',
        average_confidence_score: 0,
        engagement_trend: 'no_data'
      },
      canary_analysis_files: []
    }
  } finally {
    loading.value = false
    // Create charts after data is loaded
    setTimeout(() => {
      createCharts()
    }, 100)
  }
}

// Auto-refresh every 30 seconds
const refreshInterval = ref<number | null>(null)

// Real-time data availability checking
const startRealtimeChecking = () => {
  // Check data availability every 10 seconds
  setInterval(async () => {
    try {
      const hasDataInDigitalOcean = await checkDataAvailability()
      if (hasDataInDigitalOcean && !hasData.value) {
        console.log('🔄 New data detected! Refreshing dashboard...')
        await loadStats()
      }
    } catch (err) {
      console.error('Error in real-time data check:', err)
    }
  }, 10000) // Check every 10 seconds
}

onMounted(async () => {
  await loadStats()
  
  // Set up auto-refresh
  refreshInterval.value = window.setInterval(() => {
    loadStats()
  }, 30000)
  
  // Start real-time data availability checking
  startRealtimeChecking()
})

// Cleanup on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  if (refreshInterval.value) {
    clearInterval(refreshInterval.value)
  }
  
  // Destroy all charts
  Object.values(chartInstances.value).forEach(chart => {
    if (chart) {
      chart.destroy()
    }
  })
})
</script>

<style scoped>
.clean-working-dashboard {
  padding: 24px;
  background: #f8fafc;
  min-height: 100vh;
}

/* Loading and Error States */
.loading-container, .error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 20px;
  text-align: center;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
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
  font-weight: 500;
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
  color: #6b7280;
  font-size: 14px;
}

/* Dashboard Container */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Section Titles */
.section-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 20px;
  padding-bottom: 8px;
  border-bottom: 2px solid #e5e7eb;
}

/* Metrics Section */
.metrics-section {
  margin-bottom: 40px;
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
}

.metric-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
  transition: all 0.2s ease;
  min-width: 0;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.metric-card.primary {
  border-left: 4px solid #3b82f6;
}

.metric-icon-wrapper {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.metric-icon {
  font-size: 16px;
}

.metric-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.metric-label {
  font-size: 10px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  line-height: 1.2;
}

.metric-value {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 4px;
  line-height: 1;
}

.metric-value.no-data {
  font-size: 14px;
  font-weight: 600;
  color: #6b7280;
  font-style: italic;
}


/* Risk Assessment Section */
.risk-section {
  margin-bottom: 40px;
}

.risk-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
}

.risk-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.risk-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.risk-header {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  gap: 8px;
}

.risk-title {
  font-size: 11px;
  font-weight: 600;
  color: #374151;
  line-height: 1.2;
}

.risk-icon {
  font-size: 14px;
}

.risk-content {
  text-align: center;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.risk-score {
  font-size: 18px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 6px;
  line-height: 1;
}

.risk-status {
  font-size: 9px;
  font-weight: 600;
  padding: 3px 6px;
  border-radius: 8px;
  display: inline-block;
}

.risk-status.risk-high {
  background: #fef2f2;
  color: #dc2626;
}

.risk-status.risk-moderate {
  background: #fef3c7;
  color: #d97706;
}

.risk-status.risk-low {
  background: #f0fdf4;
  color: #16a34a;
}

/* Analytics Section */
.analytics-section {
  margin-bottom: 40px;
}

.analytics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.analytics-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.analytics-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.analytics-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.analytics-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.analytics-icon {
  font-size: 16px;
}

.analytics-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.analytics-chart {
  height: 150px;
  position: relative;
}

.analytics-stats {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
}

.stat-item .stat-label {
  font-size: 11px;
  color: #6b7280;
  font-weight: 500;
}

.stat-item .stat-value {
  font-size: 12px;
  font-weight: 600;
  color: #1f2937;
}

/* Statistics Section */
.statistics-section {
  margin-bottom: 40px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.stats-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.stats-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.stats-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.stats-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.stats-icon {
  font-size: 16px;
}

.stats-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.stat-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.stat-row:last-child {
  border-bottom: none;
}

.stat-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

.stat-value {
  font-size: 13px;
  font-weight: 600;
  color: #1f2937;
}

.stat-value.sentiment-positive {
  color: #10b981;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .metrics-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  
  .metric-card {
    padding: 12px;
  }
  
  .metric-label {
    font-size: 9px;
  }
  
  .metric-value {
    font-size: 16px;
  }
  
  .risk-grid {
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
  }
  
  .risk-card {
    padding: 12px;
  }
  
  .risk-title {
    font-size: 10px;
  }
  
  .risk-score {
    font-size: 16px;
  }
  
  .risk-status {
    font-size: 8px;
    padding: 2px 4px;
  }
}

@media (max-width: 768px) {
  .clean-working-dashboard {
    padding: 16px;
  }
  
  .metrics-grid {
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
  }
  
  .metric-card {
    padding: 8px;
  }
  
  .metric-icon-wrapper {
    width: 24px;
    height: 24px;
  }
  
  .metric-icon {
    font-size: 12px;
  }
  
  .metric-label {
    font-size: 8px;
  }
  
  .metric-value {
    font-size: 14px;
  }
  
  .risk-grid {
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
  }
  
  .risk-card {
    padding: 8px;
  }
  
  .risk-title {
    font-size: 9px;
  }
  
  .risk-icon {
    font-size: 12px;
  }
  
  .risk-score {
    font-size: 14px;
  }
  
  .risk-status {
    font-size: 7px;
    padding: 1px 3px;
  }
  
  .analytics-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
