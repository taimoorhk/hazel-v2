<template>
  <div class="optimized-stats-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
      <p class="text-sm text-[#2871B5]/70">Loading health analytics...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="text-red-500 text-2xl mb-2">⚠️</div>
      <p class="text-sm text-red-600">{{ error }}</p>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else-if="statsData" class="dashboard-content">
      <!-- Compact Health Overview Cards -->
      <div class="health-overview-grid">
        <div class="health-card overall">
          <div class="card-icon">💚</div>
          <div class="card-content">
            <div class="card-label">Overall Health</div>
            <div class="card-value">{{ Math.round(overallHealthScore * 10) }}%</div>
            <div class="card-status excellent">Excellent</div>
          </div>
        </div>

        <div class="health-card cognitive">
          <div class="card-icon">🧠</div>
          <div class="card-content">
            <div class="card-label">Cognitive Health</div>
            <div class="card-value">{{ Math.round(cognitiveHealthScore * 10) }}%</div>
            <div class="card-status" :class="getCognitiveStatusClass()">{{ getCognitiveStatus() }}</div>
          </div>
        </div>

        <div class="health-card mental">
          <div class="card-icon">🧘</div>
          <div class="card-content">
            <div class="card-label">Mental Health</div>
            <div class="card-value">{{ Math.round(mentalHealthScore * 10) }}%</div>
            <div class="card-status" :class="getMentalHealthStatusClass()">{{ getMentalHealthStatus() }}</div>
          </div>
        </div>

        <div class="health-card physical">
          <div class="card-icon">💪</div>
          <div class="card-content">
            <div class="card-label">Physical Health</div>
            <div class="card-value">{{ Math.round(physicalHealthScore * 10) }}%</div>
            <div class="card-status" :class="getPhysicalHealthStatusClass()">{{ getPhysicalHealthStatus() }}</div>
          </div>
        </div>

        <div class="health-card social">
          <div class="card-icon">👥</div>
          <div class="card-content">
            <div class="card-label">Social Health</div>
            <div class="card-value">{{ Math.round(socialHealthScore * 10) }}%</div>
            <div class="card-status" :class="getSocialHealthStatusClass()">{{ getSocialHealthStatus() }}</div>
          </div>
        </div>

        <div class="health-card calls">
          <div class="card-icon">📞</div>
          <div class="card-content">
            <div class="card-label">Total Calls</div>
            <div class="card-value">{{ totalCalls }}</div>
            <div class="card-status active">Active</div>
          </div>
        </div>
      </div>

      <!-- Compact Risk Assessment -->
      <div class="risk-assessment-grid">
        <div class="risk-card" :class="getRiskLevel(alzheimerRiskScore)">
          <div class="risk-icon">🧠</div>
          <div class="risk-content">
            <div class="risk-title">Alzheimer's Risk</div>
            <div class="risk-score">{{ alzheimerRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getAlzheimerRiskDescription() }}</div>
          </div>
        </div>

        <div class="risk-card" :class="getRiskLevel(parkinsonRiskScore)">
          <div class="risk-icon">🤲</div>
          <div class="risk-content">
            <div class="risk-title">Parkinson's Risk</div>
            <div class="risk-score">{{ parkinsonRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getParkinsonRiskDescription() }}</div>
          </div>
        </div>

        <div class="risk-card" :class="getRiskLevel(depressionRiskScore)">
          <div class="risk-icon">😔</div>
          <div class="risk-content">
            <div class="risk-title">Depression Risk</div>
            <div class="risk-score">{{ depressionRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getDepressionRiskDescription() }}</div>
          </div>
        </div>

        <div class="risk-card" :class="getRiskLevel(anxietyRiskScore)">
          <div class="risk-icon">😰</div>
          <div class="risk-content">
            <div class="risk-title">Anxiety Risk</div>
            <div class="risk-score">{{ anxietyRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getAnxietyRiskDescription() }}</div>
          </div>
        </div>

        <div class="risk-card" :class="getRiskLevel(fallRiskScore)">
          <div class="risk-icon">⚠️</div>
          <div class="risk-content">
            <div class="risk-title">Fall Risk</div>
            <div class="risk-score">{{ fallRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getFallRiskDescription() }}</div>
          </div>
        </div>

        <div class="risk-card" :class="getRiskLevel(cognitiveRiskScore)">
          <div class="risk-icon">🧮</div>
          <div class="risk-content">
            <div class="risk-title">Cognitive Risk</div>
            <div class="risk-score">{{ cognitiveRiskScore.toFixed(1) }}/10</div>
            <div class="risk-description">{{ getCognitiveRiskDescription() }}</div>
          </div>
        </div>
      </div>

      <!-- Interactive Charts Section -->
      <div class="charts-section">
        <h3 class="section-title">📊 Health Analytics</h3>
        
        <!-- First Row of Charts -->
        <div class="charts-row">
          <div class="chart-container">
            <h4 class="chart-title">Mood Distribution</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="moodChartData"
                type="doughnut"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Engagement Levels</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="engagementChartData"
                type="bar"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Energy Levels</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="energyLevelChartData"
                type="pie"
                :options="compactChartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Second Row of Charts -->
        <div class="charts-row">
          <div class="chart-container">
            <h4 class="chart-title">Cognitive Function</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="cognitiveFunctionChartData"
                type="doughnut"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Memory Recall</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="memoryRecallChartData"
                type="bar"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Sleep Quality</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="sleepQualityChartData"
                type="pie"
                :options="compactChartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Third Row of Charts -->
        <div class="charts-row">
          <div class="chart-container">
            <h4 class="chart-title">Pain Levels</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="painLevelsChartData"
                type="doughnut"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Mobility Assessment</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="mobilityAssessmentChartData"
                type="bar"
                :options="compactChartOptions"
              />
            </div>
          </div>

          <div class="chart-container">
            <h4 class="chart-title">Medication Compliance</h4>
            <div class="chart-wrapper">
              <StatsChart
                :data="medicationComplianceChartData"
                type="pie"
                :options="compactChartOptions"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Numeric Statistics -->
      <div class="numeric-stats-section">
        <h3 class="section-title">📈 Detailed Statistics</h3>
        
        <div class="stats-grid">
          <!-- Call Statistics -->
          <div class="stats-card">
            <h4 class="stats-title">📞 Call Statistics</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Total Calls:</span>
                <span class="stat-value">{{ totalCalls }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Total Duration:</span>
                <span class="stat-value">{{ formatDuration(totalDuration) }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Average Duration:</span>
                <span class="stat-value">{{ formatDuration(averageDuration) }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Last Call:</span>
                <span class="stat-value">{{ formatLastCall() }}</span>
              </div>
            </div>
          </div>

          <!-- Health Scores -->
          <div class="stats-card">
            <h4 class="stats-title">🏥 Health Scores</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Overall Health:</span>
                <span class="stat-value">{{ (overallHealthScore * 10).toFixed(1) }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Cognitive Health:</span>
                <span class="stat-value">{{ (cognitiveHealthScore * 10).toFixed(1) }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Mental Health:</span>
                <span class="stat-value">{{ (mentalHealthScore * 10).toFixed(1) }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Physical Health:</span>
                <span class="stat-value">{{ (physicalHealthScore * 10).toFixed(1) }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Social Health:</span>
                <span class="stat-value">{{ (socialHealthScore * 10).toFixed(1) }}%</span>
              </div>
            </div>
          </div>

          <!-- Risk Scores -->
          <div class="stats-card">
            <h4 class="stats-title">⚠️ Risk Assessment</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Alzheimer's Risk:</span>
                <span class="stat-value" :class="getRiskClass(alzheimerRiskScore)">{{ alzheimerRiskScore.toFixed(1) }}/10</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Parkinson's Risk:</span>
                <span class="stat-value" :class="getRiskClass(parkinsonRiskScore)">{{ parkinsonRiskScore.toFixed(1) }}/10</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Depression Risk:</span>
                <span class="stat-value" :class="getRiskClass(depressionRiskScore)">{{ depressionRiskScore.toFixed(1) }}/10</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Anxiety Risk:</span>
                <span class="stat-value" :class="getRiskClass(anxietyRiskScore)">{{ anxietyRiskScore.toFixed(1) }}/10</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Fall Risk:</span>
                <span class="stat-value" :class="getRiskClass(fallRiskScore)">{{ fallRiskScore.toFixed(1) }}/10</span>
              </div>
            </div>
          </div>

          <!-- Health Conditions -->
          <div class="stats-card">
            <h4 class="stats-title">🏥 Health Conditions</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Diagnosed:</span>
                <span class="stat-value">{{ healthConditions.diagnosed_count }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Suspected:</span>
                <span class="stat-value">{{ healthConditions.suspected_count }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Risk Factors:</span>
                <span class="stat-value">{{ healthConditions.risk_factors_count }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Monitored:</span>
                <span class="stat-value">{{ healthConditions.monitored_count }}</span>
              </div>
            </div>
          </div>

          <!-- Sentiment Analysis -->
          <div class="stats-card">
            <h4 class="stats-title">😊 Sentiment Analysis</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Average Sentiment:</span>
                <span class="stat-value">{{ averageSentiment }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Positive:</span>
                <span class="stat-value">{{ sentimentDistribution.positive || 0 }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Neutral:</span>
                <span class="stat-value">{{ sentimentDistribution.neutral || 0 }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Negative:</span>
                <span class="stat-value">{{ sentimentDistribution.negative || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Conversation Quality -->
          <div class="stats-card">
            <h4 class="stats-title">💬 Conversation Quality</h4>
            <div class="stats-content">
              <div class="stat-item">
                <span class="stat-label">Quality:</span>
                <span class="stat-value">{{ conversationQuality }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Confidence:</span>
                <span class="stat-value">{{ (averageConfidenceScore * 100).toFixed(1) }}%</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Data Completeness:</span>
                <span class="stat-value">{{ dataCompleteness }}</span>
              </div>
              <div class="stat-item">
                <span class="stat-label">Engagement Trend:</span>
                <span class="stat-value">{{ engagementTrend }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Last Updated -->
      <div class="last-updated">
        <p class="text-xs text-gray-500">
          Last updated: {{ new Date().toLocaleString() }} | Auto-refresh every 30 seconds
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import StatsChart from '@/components/StatsChart.vue'

const props = defineProps<{
  profileId: number
  accountId: number
  isElderlyProfile: boolean
}>()

// Reactive data
const loading = ref(true)
const error = ref<string | null>(null)
const statsData = ref<any>(null)

// Compact chart options for smaller charts
const compactChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom' as const,
      labels: {
        padding: 8,
        font: {
          size: 10
        }
      }
    }
  }
}

// Computed properties for health scores
const overallHealthScore = computed(() => statsData.value?.aggregated_health_summary?.overall_health_score || 0)
const cognitiveHealthScore = computed(() => statsData.value?.aggregated_health_summary?.cognitive_health_score || 0)
const mentalHealthScore = computed(() => statsData.value?.aggregated_health_summary?.mental_health_score || 0)
const physicalHealthScore = computed(() => statsData.value?.aggregated_health_summary?.physical_health_score || 0)
const socialHealthScore = computed(() => statsData.value?.aggregated_health_summary?.social_health_score || 0)

// Computed properties for risk scores
const alzheimerRiskScore = computed(() => statsData.value?.aggregated_health_summary?.alzheimer_risk_score || 0)
const parkinsonRiskScore = computed(() => statsData.value?.aggregated_health_summary?.parkinson_risk_score || 0)
const depressionRiskScore = computed(() => statsData.value?.aggregated_health_summary?.depression_risk_score || 0)
const anxietyRiskScore = computed(() => statsData.value?.aggregated_health_summary?.anxiety_risk_score || 0)
const fallRiskScore = computed(() => statsData.value?.aggregated_health_summary?.fall_risk_score || 0)
const cognitiveRiskScore = computed(() => statsData.value?.aggregated_health_summary?.cognitive_risk_score || 0)

// Computed properties for basic metrics
const totalCalls = computed(() => statsData.value?.aggregated_health_summary?.total_calls || 0)
const totalDuration = computed(() => statsData.value?.aggregated_health_summary?.total_duration || 0)
const averageDuration = computed(() => totalCalls.value > 0 ? totalDuration.value / totalCalls.value : 0)
const healthConditions = computed(() => ({
  diagnosed_count: statsData.value?.aggregated_health_summary?.diagnosed_conditions_count || 0,
  suspected_count: statsData.value?.aggregated_health_summary?.suspected_conditions_count || 0,
  risk_factors_count: statsData.value?.aggregated_health_summary?.risk_factors_count || 0,
  monitored_count: statsData.value?.aggregated_health_summary?.monitored_conditions_count || 0
}))

// Additional computed properties for detailed stats
const averageSentiment = computed(() => statsData.value?.aggregated_health_summary?.average_sentiment || 'neutral')
const sentimentDistribution = computed(() => statsData.value?.aggregated_health_summary?.sentiment_distribution || {})
const conversationQuality = computed(() => statsData.value?.aggregated_health_summary?.conversation_quality || 'unknown')
const averageConfidenceScore = computed(() => statsData.value?.aggregated_health_summary?.average_confidence_score || 0)
const dataCompleteness = computed(() => statsData.value?.aggregated_health_summary?.data_completeness || 'unknown')
const engagementTrend = computed(() => statsData.value?.aggregated_health_summary?.engagement_trend || 'stable')

// Chart data computed properties (same as before but optimized)
const moodChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
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

const painLevelsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const pains = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.pain_level)
  const painCounts = pains.reduce((acc: any, pain: string) => {
    acc[pain] = (acc[pain] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(painCounts),
    datasets: [{
      data: Object.values(painCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const mobilityAssessmentChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const mobilities = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.mobility)
  const mobilityCounts = mobilities.reduce((acc: any, mobility: string) => {
    acc[mobility] = (acc[mobility] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(mobilityCounts),
    datasets: [{
      data: Object.values(mobilityCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
    }]
  }
})

const medicationComplianceChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const compliances = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.medication_compliance)
  const complianceCounts = compliances.reduce((acc: any, compliance: string) => {
    acc[compliance] = (acc[compliance] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(complianceCounts),
    datasets: [{
      data: Object.values(complianceCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
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

const getRiskLevel = (score: number) => {
  if (score <= 3) return 'low'
  if (score <= 6) return 'moderate'
  return 'high'
}

const getRiskClass = (score: number) => {
  const level = getRiskLevel(score)
  return `risk-${level}`
}

const getCognitiveStatus = () => {
  const score = cognitiveHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getCognitiveStatusClass = () => {
  const score = cognitiveHealthScore.value
  if (score >= 8) return 'excellent'
  if (score >= 6) return 'good'
  return 'fair'
}

const getMentalHealthStatus = () => {
  const score = mentalHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getMentalHealthStatusClass = () => {
  const score = mentalHealthScore.value
  if (score >= 8) return 'excellent'
  if (score >= 6) return 'good'
  return 'fair'
}

const getPhysicalHealthStatus = () => {
  const score = physicalHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getPhysicalHealthStatusClass = () => {
  const score = physicalHealthScore.value
  if (score >= 8) return 'excellent'
  if (score >= 6) return 'good'
  return 'fair'
}

const getSocialHealthStatus = () => {
  const score = socialHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getSocialHealthStatusClass = () => {
  const score = socialHealthScore.value
  if (score >= 8) return 'excellent'
  if (score >= 6) return 'good'
  return 'fair'
}

const getAlzheimerRiskDescription = () => {
  const score = alzheimerRiskScore.value
  if (score <= 3) return 'Low risk - Good cognitive health'
  if (score <= 6) return 'Moderate risk - Monitor cognitive function'
  return 'High risk - Consider cognitive assessment'
}

const getParkinsonRiskDescription = () => {
  const score = parkinsonRiskScore.value
  if (score <= 3) return 'Low risk - No motor symptoms'
  if (score <= 6) return 'Moderate risk - Monitor motor function'
  return 'High risk - Consider neurological evaluation'
}

const getDepressionRiskDescription = () => {
  const score = depressionRiskScore.value
  if (score <= 3) return 'Low risk - Good mood indicators'
  if (score <= 6) return 'Moderate risk - Monitor mood changes'
  return 'High risk - Consider mental health support'
}

const getAnxietyRiskDescription = () => {
  const score = anxietyRiskScore.value
  if (score <= 3) return 'Low risk - Low anxiety levels'
  if (score <= 6) return 'Moderate risk - Monitor stress levels'
  return 'High risk - Consider anxiety management'
}

const getFallRiskDescription = () => {
  const score = fallRiskScore.value
  if (score <= 3) return 'Low risk - Good balance and mobility'
  if (score <= 6) return 'Moderate risk - Monitor balance'
  return 'High risk - Implement fall prevention'
}

const getCognitiveRiskDescription = () => {
  const score = cognitiveRiskScore.value
  if (score <= 3) return 'Low risk - Strong cognitive function'
  if (score <= 6) return 'Moderate risk - Monitor cognitive changes'
  return 'High risk - Consider cognitive assessment'
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
      console.log('📊 Loaded optimized stats data:', result.data)
    } else {
      throw new Error(result.message || 'Failed to load enhanced canary data')
    }
  } catch (err) {
    console.error('Error loading optimized stats:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load stats'
    
    // Fallback to basic data structure
    statsData.value = {
      aggregated_health_summary: {
        total_calls: 0,
        total_duration: 0,
        overall_health_score: 0,
        cognitive_health_score: 0,
        mental_health_score: 0,
        physical_health_score: 0,
        social_health_score: 0,
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
        average_sentiment: 'neutral',
        sentiment_distribution: {},
        conversation_quality: 'unknown',
        average_confidence_score: 0,
        data_completeness: 'unknown',
        engagement_trend: 'stable'
      },
      canary_analysis_files: []
    }
  } finally {
    loading.value = false
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
})
</script>

<style scoped>
.optimized-stats-dashboard {
  padding: 12px;
  background: transparent;
}

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
}

/* Compact Health Overview Cards */
.health-overview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}

.health-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 10px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.health-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.card-icon {
  font-size: 20px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  background: #f3f4f6;
}

.card-content {
  flex: 1;
  min-width: 0;
}

.card-label {
  font-size: 11px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.card-value {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 2px;
  line-height: 1;
}

.card-status {
  font-size: 10px;
  font-weight: 600;
  padding: 2px 6px;
  border-radius: 8px;
  display: inline-block;
}

.card-status.excellent {
  background: #dcfce7;
  color: #166534;
}

.card-status.good {
  background: #dbeafe;
  color: #1e40af;
}

.card-status.fair {
  background: #fef3c7;
  color: #92400e;
}

.card-status.active {
  background: #f3f4f6;
  color: #374151;
}

/* Compact Risk Assessment */
.risk-assessment-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-bottom: 20px;
}

.risk-card {
  background: white;
  border-radius: 8px;
  padding: 14px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.risk-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.risk-card.low {
  border-left: 3px solid #10b981;
}

.risk-card.moderate {
  border-left: 3px solid #f59e0b;
}

.risk-card.high {
  border-left: 3px solid #ef4444;
}

.risk-icon {
  font-size: 16px;
  margin-bottom: 8px;
}

.risk-content {
  flex: 1;
}

.risk-title {
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 4px;
}

.risk-score {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 4px;
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
  font-size: 10px;
  color: #6b7280;
  line-height: 1.3;
}

/* Charts Section */
.charts-section {
  margin-bottom: 20px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.charts-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}

.chart-container {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.chart-container:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.chart-title {
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 12px;
  text-align: center;
}

.chart-wrapper {
  height: 150px;
  position: relative;
}

/* Numeric Statistics Section */
.numeric-stats-section {
  margin-bottom: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 12px;
}

.stats-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stats-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.stats-title {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.stats-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 0;
  border-bottom: 1px solid #f3f4f6;
}

.stat-item:last-child {
  border-bottom: none;
}

.stat-label {
  font-size: 11px;
  color: #6b7280;
  font-weight: 500;
}

.stat-value {
  font-size: 12px;
  font-weight: 600;
  color: #111827;
}

.stat-value.risk-low {
  color: #10b981;
}

.stat-value.risk-moderate {
  color: #f59e0b;
}

.stat-value.risk-high {
  color: #ef4444;
}

.last-updated {
  text-align: center;
  padding: 12px;
  background: #f9fafb;
  border-radius: 6px;
  margin-top: 16px;
}
</style>
