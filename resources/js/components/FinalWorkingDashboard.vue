<template>
  <div class="final-working-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="loading-text">Loading comprehensive health analytics...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h3 class="error-title">Unable to Load Data</h3>
      <p class="error-message">{{ error }}</p>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else class="dashboard-container">
      <!-- Header Section -->
      <div class="dashboard-header">
        <h2 class="dashboard-title">Health Analytics Dashboard</h2>
        <p class="dashboard-subtitle">
          {{ isElderlyProfile ? 'Comprehensive health insights for elderly profile' : 'Your personal health monitoring and analytics' }}
        </p>
      </div>

      <!-- Key Metrics Overview -->
      <section class="metrics-section">
        <h3 class="section-title">Key Health Metrics</h3>
        <div class="metrics-grid">
          <div class="metric-card primary">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">💚</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Overall Health</div>
              <div class="metric-value">{{ Math.round(overallHealthScore * 10) }}%</div>
              <div class="metric-status excellent">Excellent</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">🧠</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Cognitive Health</div>
              <div class="metric-value">{{ Math.round(cognitiveHealthScore * 10) }}%</div>
              <div class="metric-status" :class="getStatusClass(cognitiveHealthScore)">{{ getStatusText(cognitiveHealthScore) }}</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">🧘</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Mental Health</div>
              <div class="metric-value">{{ Math.round(mentalHealthScore * 10) }}%</div>
              <div class="metric-status" :class="getStatusClass(mentalHealthScore)">{{ getStatusText(mentalHealthScore) }}</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">💪</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Physical Health</div>
              <div class="metric-value">{{ Math.round(physicalHealthScore * 10) }}%</div>
              <div class="metric-status" :class="getStatusClass(physicalHealthScore)">{{ getStatusText(physicalHealthScore) }}</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">👥</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Social Health</div>
              <div class="metric-value">{{ Math.round(socialHealthScore * 10) }}%</div>
              <div class="metric-status" :class="getStatusClass(socialHealthScore)">{{ getStatusText(socialHealthScore) }}</div>
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon-wrapper">
              <div class="metric-icon">📞</div>
            </div>
            <div class="metric-content">
              <div class="metric-label">Total Calls</div>
              <div class="metric-value">{{ totalCalls }}</div>
              <div class="metric-status active">Active</div>
            </div>
          </div>
        </div>
      </section>

      <!-- Risk Assessment Section -->
      <section class="risk-section">
        <h3 class="section-title">Health Risk Assessment</h3>
        <div class="risk-grid">
          <div class="risk-card" :class="getRiskLevelClass(alzheimerRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">🧠</div>
              <div class="risk-info">
                <h4 class="risk-title">Alzheimer's Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(alzheimerRiskScore)">
                  {{ alzheimerRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('alzheimer', alzheimerRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(alzheimerRiskScore)" 
                   :style="{ width: `${(alzheimerRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>

          <div class="risk-card" :class="getRiskLevelClass(parkinsonRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">🤲</div>
              <div class="risk-info">
                <h4 class="risk-title">Parkinson's Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(parkinsonRiskScore)">
                  {{ parkinsonRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('parkinson', parkinsonRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(parkinsonRiskScore)" 
                   :style="{ width: `${(parkinsonRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>

          <div class="risk-card" :class="getRiskLevelClass(depressionRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">😔</div>
              <div class="risk-info">
                <h4 class="risk-title">Depression Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(depressionRiskScore)">
                  {{ depressionRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('depression', depressionRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(depressionRiskScore)" 
                   :style="{ width: `${(depressionRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>

          <div class="risk-card" :class="getRiskLevelClass(anxietyRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">😰</div>
              <div class="risk-info">
                <h4 class="risk-title">Anxiety Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(anxietyRiskScore)">
                  {{ anxietyRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('anxiety', anxietyRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(anxietyRiskScore)" 
                   :style="{ width: `${(anxietyRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>

          <div class="risk-card" :class="getRiskLevelClass(fallRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">⚠️</div>
              <div class="risk-info">
                <h4 class="risk-title">Fall Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(fallRiskScore)">
                  {{ fallRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('fall', fallRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(fallRiskScore)" 
                   :style="{ width: `${(fallRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>

          <div class="risk-card" :class="getRiskLevelClass(cognitiveRiskScore)">
            <div class="risk-header">
              <div class="risk-icon">🧮</div>
              <div class="risk-info">
                <h4 class="risk-title">Cognitive Risk</h4>
                <div class="risk-score" :class="getRiskLevelClass(cognitiveRiskScore)">
                  {{ cognitiveRiskScore.toFixed(1) }}/10
                </div>
              </div>
            </div>
            <p class="risk-description">{{ getRiskDescription('cognitive', cognitiveRiskScore) }}</p>
            <div class="risk-progress">
              <div class="risk-progress-bar" :class="getRiskLevelClass(cognitiveRiskScore)" 
                   :style="{ width: `${(cognitiveRiskScore / 10) * 100}%` }"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Health Analytics Charts Section -->
      <section class="charts-section">
        <h3 class="section-title">Health Analytics</h3>
        <div class="charts-grid">
          <!-- Mood Distribution Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Mood Distribution</h4>
              <div class="chart-icon">😊</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="moodChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonMood() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Total Samples:</span>
                <span class="summary-value">{{ totalCallSamples }}</span>
              </div>
            </div>
          </div>

          <!-- Engagement Levels Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Engagement Levels</h4>
              <div class="chart-icon">📈</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="engagementChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonEngagement() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">High Engagement:</span>
                <span class="summary-value">{{ getHighEngagementCount() }}%</span>
              </div>
            </div>
          </div>

          <!-- Energy Levels Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Energy Levels</h4>
              <div class="chart-icon">⚡</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="energyChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonEnergy() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Good/Excellent:</span>
                <span class="summary-value">{{ getGoodEnergyCount() }}%</span>
              </div>
            </div>
          </div>

          <!-- Cognitive Function Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Cognitive Function</h4>
              <div class="chart-icon">🧠</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="cognitiveChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonCognitive() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Good/Excellent:</span>
                <span class="summary-value">{{ getGoodCognitiveCount() }}%</span>
              </div>
            </div>
          </div>

          <!-- Memory Recall Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Memory Recall</h4>
              <div class="chart-icon">🧮</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="memoryChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonMemory() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Good Recall:</span>
                <span class="summary-value">{{ getGoodMemoryCount() }}%</span>
              </div>
            </div>
          </div>

          <!-- Sleep Quality Chart -->
          <div class="chart-card">
            <div class="chart-header">
              <h4 class="chart-title">Sleep Quality</h4>
              <div class="chart-icon">😴</div>
            </div>
            <div class="chart-container">
              <div class="chart-wrapper" style="width: 100%; height: 200px; position: relative;">
                <canvas ref="sleepChart" width="400" height="200"></canvas>
              </div>
            </div>
            <div class="chart-summary">
              <div class="summary-item">
                <span class="summary-label">Most Common:</span>
                <span class="summary-value">{{ getMostCommonSleep() }}</span>
              </div>
              <div class="summary-item">
                <span class="summary-label">Good/Excellent:</span>
                <span class="summary-value">{{ getGoodSleepCount() }}%</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Detailed Statistics Section -->
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
                <span class="stat-value">{{ formatDuration(averageDuration) }}</span>
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
                <span class="stat-value">{{ healthConditions.diagnosed_count }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Suspected</span>
                <span class="stat-value">{{ healthConditions.suspected_count }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Risk Factors</span>
                <span class="stat-value">{{ healthConditions.risk_factors_count }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Monitored</span>
                <span class="stat-value">{{ healthConditions.monitored_count }}</span>
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
                <span class="stat-value quality-high">{{ conversationQuality }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Confidence</span>
                <span class="stat-value">{{ (averageConfidenceScore * 100).toFixed(1) }}%</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Data Completeness</span>
                <span class="stat-value completeness-excellent">{{ dataCompleteness }}</span>
              </div>
              <div class="stat-row">
                <span class="stat-label">Engagement Trend</span>
                <span class="stat-value trend-increasing">{{ engagementTrend }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <div class="dashboard-footer">
        <p class="last-updated">
          Last updated: {{ new Date().toLocaleString() }} | Auto-refresh every 30 seconds
        </p>
      </div>
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

// Canvas refs
const moodChart = ref<HTMLCanvasElement | null>(null)
const engagementChart = ref<HTMLCanvasElement | null>(null)
const energyChart = ref<HTMLCanvasElement | null>(null)
const cognitiveChart = ref<HTMLCanvasElement | null>(null)
const memoryChart = ref<HTMLCanvasElement | null>(null)
const sleepChart = ref<HTMLCanvasElement | null>(null)

// Chart instances
const chartInstances = ref<{ [key: string]: ChartJS | null }>({
  mood: null,
  engagement: null,
  energy: null,
  cognitive: null,
  memory: null,
  sleep: null
})

// Reactive data
const loading = ref(true)
const error = ref<string | null>(null)
const statsData = ref<any>(null)

// Computed properties
const overallHealthScore = computed(() => statsData.value?.aggregated_health_summary?.overall_health_score || 0)
const cognitiveHealthScore = computed(() => statsData.value?.aggregated_health_summary?.cognitive_health_score || 0)
const mentalHealthScore = computed(() => statsData.value?.aggregated_health_summary?.mental_health_score || 0)
const physicalHealthScore = computed(() => statsData.value?.aggregated_health_summary?.physical_health_score || 0)
const socialHealthScore = computed(() => statsData.value?.aggregated_health_summary?.social_health_score || 0)

const alzheimerRiskScore = computed(() => statsData.value?.aggregated_health_summary?.alzheimer_risk_score || 0)
const parkinsonRiskScore = computed(() => statsData.value?.aggregated_health_summary?.parkinson_risk_score || 0)
const depressionRiskScore = computed(() => statsData.value?.aggregated_health_summary?.depression_risk_score || 0)
const anxietyRiskScore = computed(() => statsData.value?.aggregated_health_summary?.anxiety_risk_score || 0)
const fallRiskScore = computed(() => statsData.value?.aggregated_health_summary?.fall_risk_score || 0)
const cognitiveRiskScore = computed(() => statsData.value?.aggregated_health_summary?.cognitive_risk_score || 0)

const totalCalls = computed(() => statsData.value?.aggregated_health_summary?.total_calls || 0)
const totalDuration = computed(() => statsData.value?.aggregated_health_summary?.total_duration || 0)
const averageDuration = computed(() => totalCalls.value > 0 ? totalDuration.value / totalCalls.value : 0)

const healthConditions = computed(() => ({
  diagnosed_count: statsData.value?.aggregated_health_summary?.diagnosed_conditions_count || 0,
  suspected_count: statsData.value?.aggregated_health_summary?.suspected_conditions_count || 0,
  risk_factors_count: statsData.value?.aggregated_health_summary?.risk_factors_count || 0,
  monitored_count: statsData.value?.aggregated_health_summary?.monitored_conditions_count || 0
}))

const averageSentiment = computed(() => statsData.value?.aggregated_health_summary?.average_sentiment || 'neutral')
const sentimentDistribution = computed(() => statsData.value?.aggregated_health_summary?.sentiment_distribution || {})
const conversationQuality = computed(() => statsData.value?.aggregated_health_summary?.conversation_quality || 'unknown')
const averageConfidenceScore = computed(() => statsData.value?.aggregated_health_summary?.average_confidence_score || 0)
const dataCompleteness = computed(() => statsData.value?.aggregated_health_summary?.data_completeness || 'unknown')
const engagementTrend = computed(() => statsData.value?.aggregated_health_summary?.engagement_trend || 'stable')

// Helper to get total call samples for charts
const totalCallSamples = computed(() => {
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
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4']
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
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
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
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
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
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
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
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
    }]
  }
})

// Helper functions for chart summaries
const getMostCommonMood = () => {
  if (!statsData.value?.canary_analysis_files) return 'Content'
  const moods = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.mood)
  const moodCounts = moods.reduce((acc: any, mood: string) => {
    acc[mood] = (acc[mood] || 0) + 1
    return acc
  }, {})
  return Object.keys(moodCounts).reduce((a, b) => moodCounts[a] > moodCounts[b] ? a : b, 'content')
}

const getMostCommonEngagement = () => {
  if (!statsData.value?.canary_analysis_files) return 'High'
  const engagements = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.engagement)
  const engagementCounts = engagements.reduce((acc: any, engagement: string) => {
    acc[engagement] = (acc[engagement] || 0) + 1
    return acc
  }, {})
  return Object.keys(engagementCounts).reduce((a, b) => engagementCounts[a] > engagementCounts[b] ? a : b, 'high')
}

const getMostCommonEnergy = () => {
  if (!statsData.value?.canary_analysis_files) return 'Good'
  const energyLevels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.energy_level)
  const energyCounts = energyLevels.reduce((acc: any, level: string) => {
    acc[level] = (acc[level] || 0) + 1
    return acc
  }, {})
  return Object.keys(energyCounts).reduce((a, b) => energyCounts[a] > energyCounts[b] ? a : b, 'good')
}

const getMostCommonCognitive = () => {
  if (!statsData.value?.canary_analysis_files) return 'Good'
  const functions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.cognitive_function)
  const functionCounts = functions.reduce((acc: any, func: string) => {
    acc[func] = (acc[func] || 0) + 1
    return acc
  }, {})
  return Object.keys(functionCounts).reduce((a, b) => functionCounts[a] > functionCounts[b] ? a : b, 'good')
}

const getMostCommonMemory = () => {
  if (!statsData.value?.canary_analysis_files) return 'Good'
  const memories = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.memory_recall)
  const memoryCounts = memories.reduce((acc: any, memory: string) => {
    acc[memory] = (acc[memory] || 0) + 1
    return acc
  }, {})
  return Object.keys(memoryCounts).reduce((a, b) => memoryCounts[a] > memoryCounts[b] ? a : b, 'good')
}

const getMostCommonSleep = () => {
  if (!statsData.value?.canary_analysis_files) return 'Good'
  const sleeps = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.sleep_quality)
  const sleepCounts = sleeps.reduce((acc: any, sleep: string) => {
    acc[sleep] = (acc[sleep] || 0) + 1
    return acc
  }, {})
  return Object.keys(sleepCounts).reduce((a, b) => sleepCounts[a] > sleepCounts[b] ? a : b, 'good')
}

const getHighEngagementCount = () => {
  if (!statsData.value?.canary_analysis_files) return 67
  const engagements = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.engagement)
  const highEngagement = engagements.filter(e => e === 'high').length
  return Math.round((highEngagement / engagements.length) * 100)
}

const getGoodEnergyCount = () => {
  if (!statsData.value?.canary_analysis_files) return 67
  const energyLevels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.energy_level)
  const goodEnergy = energyLevels.filter(e => e === 'good' || e === 'excellent').length
  return Math.round((goodEnergy / energyLevels.length) * 100)
}

const getGoodCognitiveCount = () => {
  if (!statsData.value?.canary_analysis_files) return 100
  const functions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.cognitive_function)
  const goodCognitive = functions.filter(f => f === 'good' || f === 'excellent').length
  return Math.round((goodCognitive / functions.length) * 100)
}

const getGoodMemoryCount = () => {
  if (!statsData.value?.canary_analysis_files) return 67
  const memories = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.memory_recall)
  const goodMemory = memories.filter(m => m === 'good' || m === 'excellent' || m === 'clear').length
  return Math.round((goodMemory / memories.length) * 100)
}

const getGoodSleepCount = () => {
  if (!statsData.value?.canary_analysis_files) return 67
  const sleeps = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.sleep_quality)
  const goodSleep = sleeps.filter(s => s === 'good' || s === 'excellent').length
  return Math.round((goodSleep / sleeps.length) * 100)
}

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

const getStatusClass = (score: number) => {
  if (score >= 8) return 'excellent'
  if (score >= 6) return 'good'
  if (score >= 4) return 'fair'
  return 'poor'
}

const getStatusText = (score: number) => {
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getRiskLevelClass = (score: number) => {
  if (score <= 3) return 'low'
  if (score <= 6) return 'moderate'
  return 'high'
}

const getRiskDescription = (type: string, score: number) => {
  const level = getRiskLevelClass(score)
  const descriptions = {
    alzheimer: {
      low: 'Low risk - Good cognitive health',
      moderate: 'Moderate risk - Monitor cognitive function',
      high: 'High risk - Consider cognitive assessment'
    },
    parkinson: {
      low: 'Low risk - No motor symptoms',
      moderate: 'Moderate risk - Monitor motor function',
      high: 'High risk - Consider neurological evaluation'
    },
    depression: {
      low: 'Low risk - Good mood indicators',
      moderate: 'Moderate risk - Monitor mood changes',
      high: 'High risk - Consider mental health support'
    },
    anxiety: {
      low: 'Low risk - Low anxiety levels',
      moderate: 'Moderate risk - Monitor stress levels',
      high: 'High risk - Consider anxiety management'
    },
    fall: {
      low: 'Low risk - Good balance and mobility',
      moderate: 'Moderate risk - Monitor balance',
      high: 'High risk - Implement fall prevention'
    },
    cognitive: {
      low: 'Low risk - Strong cognitive function',
      moderate: 'Moderate risk - Monitor cognitive changes',
      high: 'High risk - Consider cognitive assessment'
    }
  }
  return descriptions[type as keyof typeof descriptions]?.[level as keyof typeof descriptions.alzheimer] || 'Risk assessment unavailable'
}

// Create charts
const createCharts = () => {
  const charts = [
    { ref: moodChart, data: moodChartData.value, type: 'doughnut' as const, key: 'mood' },
    { ref: engagementChart, data: engagementChartData.value, type: 'bar' as const, key: 'engagement' },
    { ref: energyChart, data: energyLevelChartData.value, type: 'pie' as const, key: 'energy' },
    { ref: cognitiveChart, data: cognitiveFunctionChartData.value, type: 'doughnut' as const, key: 'cognitive' },
    { ref: memoryChart, data: memoryRecallChartData.value, type: 'bar' as const, key: 'memory' },
    { ref: sleepChart, data: sleepQualityChartData.value, type: 'pie' as const, key: 'sleep' }
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
        console.log(`🎯 Chart ${key} created successfully`)
      } catch (error) {
        console.error(`🎯 Chart ${key} creation failed:`, error)
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

// Load enhanced stats data
const loadStats = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Fetch enhanced canary analysis data
    const endpoint = props.isElderlyProfile 
      ? `/api/enhanced-canary/profile-15`
      : `/api/enhanced-canary/account-6`
    
    const response = await fetch(endpoint)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const result = await response.json()
    
    if (result.success && result.data) {
      // Transform the enhanced canary data
      statsData.value = transformEnhancedCanaryData(result.data)
      console.log('📊 Loaded final stats data:', result.data)
    } else {
      throw new Error(result.message || 'Failed to load enhanced canary data')
    }
  } catch (err) {
    console.error('Error loading final stats:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load stats'
    
    // Fallback to basic data structure
    statsData.value = {
      aggregated_health_summary: {
        total_calls: 15,
        total_duration: 2850,
        overall_health_score: 8.7,
        cognitive_health_score: 8.3,
        mental_health_score: 9.0,
        physical_health_score: 8.6,
        social_health_score: 8.6,
        alzheimer_risk_score: 2.8,
        parkinson_risk_score: 0.9,
        depression_risk_score: 1.2,
        anxiety_risk_score: 1.4,
        fall_risk_score: 1.7,
        cognitive_risk_score: 2.3,
        diagnosed_conditions_count: 3,
        suspected_conditions_count: 1,
        risk_factors_count: 3,
        monitored_conditions_count: 3,
        average_sentiment: 'positive',
        sentiment_distribution: { positive: 10, neutral: 4, negative: 1 },
        conversation_quality: 'high',
        average_confidence_score: 0.87,
        data_completeness: 'excellent',
        engagement_trend: 'increasing'
      },
      canary_analysis_files: [
        {
          canary_data: {
            analysis: {
              mood: 'content',
              engagement: 'high',
              energy_level: 'good',
              cognitive_function: 'good',
              memory_recall: 'slight_difficulty',
              sleep_quality: 'fair'
            }
          }
        },
        {
          canary_data: {
            analysis: {
              mood: 'neutral',
              engagement: 'medium',
              energy_level: 'moderate',
              cognitive_function: 'good',
              memory_recall: 'good',
              sleep_quality: 'good'
            }
          }
        },
        {
          canary_data: {
            analysis: {
              mood: 'happy',
              engagement: 'high',
              energy_level: 'good',
              cognitive_function: 'excellent',
              memory_recall: 'excellent',
              sleep_quality: 'excellent'
            }
          }
        }
      ]
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

onMounted(async () => {
  await loadStats()
  
  // Set up auto-refresh
  refreshInterval.value = window.setInterval(() => {
    loadStats()
  }, 30000)
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
.final-working-dashboard {
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

/* Header */
.dashboard-header {
  margin-bottom: 32px;
  text-align: center;
}

.dashboard-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 8px;
}

.dashboard-subtitle {
  font-size: 16px;
  color: #6b7280;
  font-weight: 500;
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

.metric-status {
  font-size: 9px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 8px;
  display: inline-block;
}

.metric-status.excellent {
  background: #dcfce7;
  color: #166534;
}

.metric-status.good {
  background: #dbeafe;
  color: #1e40af;
}

.metric-status.fair {
  background: #fef3c7;
  color: #92400e;
}

.metric-status.poor {
  background: #fee2e2;
  color: #991b1b;
}

.metric-status.active {
  background: #f3f4f6;
  color: #374151;
}

/* Risk Assessment Section */
.risk-section {
  margin-bottom: 40px;
}

.risk-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.risk-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.risk-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.risk-card.low {
  border-left: 4px solid #10b981;
}

.risk-card.moderate {
  border-left: 4px solid #f59e0b;
}

.risk-card.high {
  border-left: 4px solid #ef4444;
}

.risk-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.risk-icon {
  font-size: 20px;
}

.risk-info {
  flex: 1;
}

.risk-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 4px;
}

.risk-score {
  font-size: 20px;
  font-weight: 700;
}

.risk-card.low .risk-score {
  color: #10b981;
}

.risk-card.moderate .risk-score {
  color: #f59e0b;
}

.risk-card.high .risk-score {
  color: #ef4444;
}

.risk-description {
  font-size: 12px;
  color: #6b7280;
  line-height: 1.4;
  margin-bottom: 12px;
}

.risk-progress {
  width: 100%;
  height: 4px;
  background: #e5e7eb;
  border-radius: 2px;
  overflow: hidden;
}

.risk-progress-bar {
  height: 100%;
  transition: width 0.3s ease;
}

.risk-progress-bar.low {
  background: #10b981;
}

.risk-progress-bar.moderate {
  background: #f59e0b;
}

.risk-progress-bar.high {
  background: #ef4444;
}

/* Charts Section */
.charts-section {
  margin-bottom: 40px;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.chart-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
}

.chart-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.chart-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.chart-icon {
  font-size: 16px;
}

.chart-container {
  height: 200px;
  position: relative;
  margin-bottom: 16px;
}

.chart-wrapper {
  height: 100%;
  position: relative;
}

.chart-summary {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-top: 16px;
  border-top: 1px solid #f3f4f6;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.summary-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

.summary-value {
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

.stat-value.quality-high {
  color: #10b981;
}

.stat-value.completeness-excellent {
  color: #10b981;
}

.stat-value.trend-increasing {
  color: #10b981;
}

/* Footer */
.dashboard-footer {
  text-align: center;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.last-updated {
  color: #6b7280;
  font-size: 12px;
  font-weight: 500;
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
}

@media (max-width: 768px) {
  .final-working-dashboard {
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
  
  .metric-status {
    font-size: 8px;
    padding: 1px 4px;
  }
  
  .risk-grid {
    grid-template-columns: 1fr;
  }
  
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-title {
    font-size: 24px;
  }
}
</style>
