<template>
  <div class="enhanced-stats-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
      <p class="text-lg text-[#2871B5]/70">Loading comprehensive health analytics...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="text-red-500 text-4xl mb-4">⚠️</div>
      <p class="text-lg text-red-600 mb-2">Error loading health data</p>
      <p class="text-sm text-red-500">{{ error }}</p>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else-if="statsData" class="dashboard-content">
      <!-- Enhanced Metrics Overview -->
      <div class="metrics-overview">
        <!-- Overall Health Score -->
        <div class="metric-card health-score">
          <div class="metric-icon">💚</div>
          <div class="metric-content">
            <div class="metric-label">Overall Health</div>
            <div class="metric-value">{{ Math.round(overallHealthScore * 10) }}%</div>
            <div class="metric-trend positive">Excellent</div>
          </div>
        </div>

        <!-- Cognitive Health -->
        <div class="metric-card cognitive-health">
          <div class="metric-icon">🧠</div>
          <div class="metric-content">
            <div class="metric-label">Cognitive Health</div>
            <div class="metric-value">{{ Math.round(cognitiveHealthScore * 10) }}%</div>
            <div class="metric-trend" :class="getCognitiveTrendClass()">{{ getCognitiveTrend() }}</div>
          </div>
        </div>

        <!-- Mental Health -->
        <div class="metric-card mental-health">
          <div class="metric-icon">🧘</div>
          <div class="metric-content">
            <div class="metric-label">Mental Health</div>
            <div class="metric-value">{{ Math.round(mentalHealthScore * 10) }}%</div>
            <div class="metric-trend" :class="getMentalHealthTrendClass()">{{ getMentalHealthTrend() }}</div>
          </div>
        </div>

        <!-- Physical Health -->
        <div class="metric-card physical-health">
          <div class="metric-icon">💪</div>
          <div class="metric-content">
            <div class="metric-label">Physical Health</div>
            <div class="metric-value">{{ Math.round(physicalHealthScore * 10) }}%</div>
            <div class="metric-trend" :class="getPhysicalHealthTrendClass()">{{ getPhysicalHealthTrend() }}</div>
          </div>
        </div>

        <!-- Social Health -->
        <div class="metric-card social-health">
          <div class="metric-icon">👥</div>
          <div class="metric-content">
            <div class="metric-label">Social Health</div>
            <div class="metric-value">{{ Math.round(socialHealthScore * 10) }}%</div>
            <div class="metric-trend" :class="getSocialHealthTrendClass()">{{ getSocialHealthTrend() }}</div>
          </div>
        </div>

        <!-- Total Calls -->
        <div class="metric-card total-calls">
          <div class="metric-icon">📞</div>
          <div class="metric-content">
            <div class="metric-label">Total Calls</div>
            <div class="metric-value">{{ totalCalls }}</div>
            <div class="metric-trend positive">{{ getCallTrend() }}</div>
          </div>
        </div>
      </div>

      <!-- Risk Assessment Dashboard -->
      <div class="risk-assessment-section">
        <h3 class="section-title">🚨 Health Risk Assessment</h3>
        <div class="risk-grid">
          <!-- Alzheimer's Risk -->
          <div class="risk-card alzheimer-risk">
            <div class="risk-header">
              <span class="risk-icon">🧠</span>
              <span class="risk-title">Alzheimer's Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(alzheimerRiskScore)">
              {{ alzheimerRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(alzheimerRiskScore)"></div>
            <div class="risk-description">{{ getAlzheimerRiskDescription() }}</div>
          </div>

          <!-- Parkinson's Risk -->
          <div class="risk-card parkinson-risk">
            <div class="risk-header">
              <span class="risk-icon">🤲</span>
              <span class="risk-title">Parkinson's Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(parkinsonRiskScore)">
              {{ parkinsonRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(parkinsonRiskScore)"></div>
            <div class="risk-description">{{ getParkinsonRiskDescription() }}</div>
          </div>

          <!-- Depression Risk -->
          <div class="risk-card depression-risk">
            <div class="risk-header">
              <span class="risk-icon">😔</span>
              <span class="risk-title">Depression Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(depressionRiskScore)">
              {{ depressionRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(depressionRiskScore)"></div>
            <div class="risk-description">{{ getDepressionRiskDescription() }}</div>
          </div>

          <!-- Anxiety Risk -->
          <div class="risk-card anxiety-risk">
            <div class="risk-header">
              <span class="risk-icon">😰</span>
              <span class="risk-title">Anxiety Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(anxietyRiskScore)">
              {{ anxietyRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(anxietyRiskScore)"></div>
            <div class="risk-description">{{ getAnxietyRiskDescription() }}</div>
          </div>

          <!-- Fall Risk -->
          <div class="risk-card fall-risk">
            <div class="risk-header">
              <span class="risk-icon">⚠️</span>
              <span class="risk-title">Fall Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(fallRiskScore)">
              {{ fallRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(fallRiskScore)"></div>
            <div class="risk-description">{{ getFallRiskDescription() }}</div>
          </div>

          <!-- Cognitive Risk -->
          <div class="risk-card cognitive-risk">
            <div class="risk-header">
              <span class="risk-icon">🧮</span>
              <span class="risk-title">Cognitive Risk</span>
            </div>
            <div class="risk-score" :class="getRiskLevel(cognitiveRiskScore)">
              {{ cognitiveRiskScore.toFixed(1) }}/10
            </div>
            <div class="risk-indicator" :class="getRiskLevel(cognitiveRiskScore)"></div>
            <div class="risk-description">{{ getCognitiveRiskDescription() }}</div>
          </div>
        </div>
      </div>

      <!-- Comprehensive Health Charts -->
      <div class="charts-section">
        <h3 class="section-title">📊 Comprehensive Health Analytics</h3>
        
        <!-- Row 1: Basic Health Metrics -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Mood Distribution</h4>
            <div class="chart-container">
              <StatsChart
                :data="moodChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Engagement Levels</h4>
            <div class="chart-container">
              <StatsChart
                :data="engagementChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Energy Levels</h4>
            <div class="chart-container">
              <StatsChart
                :data="energyLevelChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 2: Cognitive Health -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Cognitive Function</h4>
            <div class="chart-container">
              <StatsChart
                :data="cognitiveFunctionChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Memory Recall</h4>
            <div class="chart-container">
              <StatsChart
                :data="memoryRecallChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Attention Span</h4>
            <div class="chart-container">
              <StatsChart
                :data="attentionSpanChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 3: Mental Health -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Depression Indicators</h4>
            <div class="chart-container">
              <StatsChart
                :data="depressionIndicatorsChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Anxiety Levels</h4>
            <div class="chart-container">
              <StatsChart
                :data="anxietyLevelsChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Stress Levels</h4>
            <div class="chart-container">
              <StatsChart
                :data="stressLevelsChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 4: Neurological Health -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Tremor Assessment</h4>
            <div class="chart-container">
              <StatsChart
                :data="tremorAssessmentChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Gait Abnormalities</h4>
            <div class="chart-container">
              <StatsChart
                :data="gaitAbnormalitiesChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Speech Clarity</h4>
            <div class="chart-container">
              <StatsChart
                :data="speechClarityChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 5: Physical Health -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Mobility Assessment</h4>
            <div class="chart-container">
              <StatsChart
                :data="mobilityAssessmentChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Pain Levels</h4>
            <div class="chart-container">
              <StatsChart
                :data="painLevelsChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Sleep Quality</h4>
            <div class="chart-container">
              <StatsChart
                :data="sleepQualityChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 6: Cardiovascular & Metabolic -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Blood Pressure Concerns</h4>
            <div class="chart-container">
              <StatsChart
                :data="bloodPressureChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Diabetes Indicators</h4>
            <div class="chart-container">
              <StatsChart
                :data="diabetesIndicatorsChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Heart Rate Status</h4>
            <div class="chart-container">
              <StatsChart
                :data="heartRateChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 7: Sensory Health -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Vision Assessment</h4>
            <div class="chart-container">
              <StatsChart
                :data="visionAssessmentChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Hearing Assessment</h4>
            <div class="chart-container">
              <StatsChart
                :data="hearingAssessmentChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Sensory Function</h4>
            <div class="chart-container">
              <StatsChart
                :data="sensoryFunctionChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>

        <!-- Row 8: Medication & Treatment -->
        <div class="chart-row">
          <div class="chart-card">
            <h4 class="chart-title">Medication Compliance</h4>
            <div class="chart-container">
              <StatsChart
                :data="medicationComplianceChartData"
                type="doughnut"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Side Effects</h4>
            <div class="chart-container">
              <StatsChart
                :data="sideEffectsChartData"
                type="bar"
                :options="chartOptions"
              />
            </div>
          </div>

          <div class="chart-card">
            <h4 class="chart-title">Treatment Adherence</h4>
            <div class="chart-container">
              <StatsChart
                :data="treatmentAdherenceChartData"
                type="pie"
                :options="chartOptions"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Health Conditions Summary -->
      <div class="health-conditions-section">
        <h3 class="section-title">🏥 Health Conditions Summary</h3>
        <div class="conditions-grid">
          <div class="condition-card diagnosed">
            <div class="condition-header">
              <span class="condition-icon">📋</span>
              <span class="condition-title">Diagnosed Conditions</span>
            </div>
            <div class="condition-count">{{ healthConditions.diagnosed_count }}</div>
            <div class="condition-description">Active medical conditions</div>
          </div>

          <div class="condition-card suspected">
            <div class="condition-header">
              <span class="condition-icon">🔍</span>
              <span class="condition-title">Suspected Conditions</span>
            </div>
            <div class="condition-count">{{ healthConditions.suspected_count }}</div>
            <div class="condition-description">Under investigation</div>
          </div>

          <div class="condition-card risk-factors">
            <div class="condition-header">
              <span class="condition-icon">⚠️</span>
              <span class="condition-title">Risk Factors</span>
            </div>
            <div class="condition-count">{{ healthConditions.risk_factors_count }}</div>
            <div class="condition-description">Identified risk factors</div>
          </div>

          <div class="condition-card monitored">
            <div class="condition-header">
              <span class="condition-icon">👁️</span>
              <span class="condition-title">Monitored Conditions</span>
            </div>
            <div class="condition-count">{{ healthConditions.monitored_count }}</div>
            <div class="condition-description">Under active monitoring</div>
          </div>
        </div>
      </div>

      <!-- Last Updated -->
      <div class="last-updated">
        <p class="text-sm text-gray-500">
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

// Chart options
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom' as const,
      labels: {
        padding: 20,
        font: {
          size: 12
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
const healthConditions = computed(() => ({
  diagnosed_count: statsData.value?.aggregated_health_summary?.diagnosed_conditions_count || 0,
  suspected_count: statsData.value?.aggregated_health_summary?.suspected_conditions_count || 0,
  risk_factors_count: statsData.value?.aggregated_health_summary?.risk_factors_count || 0,
  monitored_count: statsData.value?.aggregated_health_summary?.monitored_conditions_count || 0
}))

// Chart data computed properties
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

// Cognitive Health Charts
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

const attentionSpanChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const spans = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.attention_span)
  const spanCounts = spans.reduce((acc: any, span: string) => {
    acc[span] = (acc[span] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(spanCounts),
    datasets: [{
      data: Object.values(spanCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Mental Health Charts
const depressionIndicatorsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const indicators = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.depression_indicators)
  const indicatorCounts = indicators.reduce((acc: any, indicator: string) => {
    acc[indicator] = (acc[indicator] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(indicatorCounts),
    datasets: [{
      data: Object.values(indicatorCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const anxietyLevelsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const levels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.anxiety_level)
  const levelCounts = levels.reduce((acc: any, level: string) => {
    acc[level] = (acc[level] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(levelCounts),
    datasets: [{
      data: Object.values(levelCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
    }]
  }
})

const stressLevelsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const levels = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.stress_level)
  const levelCounts = levels.reduce((acc: any, level: string) => {
    acc[level] = (acc[level] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(levelCounts),
    datasets: [{
      data: Object.values(levelCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Neurological Health Charts
const tremorAssessmentChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const tremors = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.tremors)
  const tremorCounts = tremors.reduce((acc: any, tremor: string) => {
    acc[tremor] = (acc[tremor] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(tremorCounts),
    datasets: [{
      data: Object.values(tremorCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const gaitAbnormalitiesChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const gaits = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.gait_abnormalities)
  const gaitCounts = gaits.reduce((acc: any, gait: string) => {
    acc[gait] = (acc[gait] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(gaitCounts),
    datasets: [{
      data: Object.values(gaitCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const speechClarityChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const speeches = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.speech_clarity)
  const speechCounts = speeches.reduce((acc: any, speech: string) => {
    acc[speech] = (acc[speech] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(speechCounts),
    datasets: [{
      data: Object.values(speechCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Physical Health Charts
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

// Cardiovascular & Metabolic Charts
const bloodPressureChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const pressures = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.blood_pressure_concerns)
  const pressureCounts = pressures.reduce((acc: any, pressure: string) => {
    acc[pressure] = (acc[pressure] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(pressureCounts),
    datasets: [{
      data: Object.values(pressureCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const diabetesIndicatorsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const indicators = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.diabetes_indicators)
  const indicatorCounts = indicators.reduce((acc: any, indicator: string) => {
    acc[indicator] = (acc[indicator] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(indicatorCounts),
    datasets: [{
      data: Object.values(indicatorCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const heartRateChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const rates = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.heart_rate)
  const rateCounts = rates.reduce((acc: any, rate: string) => {
    acc[rate] = (acc[rate] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(rateCounts),
    datasets: [{
      data: Object.values(rateCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Sensory Health Charts
const visionAssessmentChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const visions = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.vision)
  const visionCounts = visions.reduce((acc: any, vision: string) => {
    acc[vision] = (acc[vision] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(visionCounts),
    datasets: [{
      data: Object.values(visionCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const hearingAssessmentChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const hearings = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.hearing)
  const hearingCounts = hearings.reduce((acc: any, hearing: string) => {
    acc[hearing] = (acc[hearing] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(hearingCounts),
    datasets: [{
      data: Object.values(hearingCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const sensoryFunctionChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const senses = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.touch_sensitivity)
  const senseCounts = senses.reduce((acc: any, sense: string) => {
    acc[sense] = (acc[sense] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(senseCounts),
    datasets: [{
      data: Object.values(senseCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Medication & Treatment Charts
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

const sideEffectsChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const effects = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.medication_side_effects)
  const effectCounts = effects.reduce((acc: any, effect: string) => {
    acc[effect] = (acc[effect] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(effectCounts),
    datasets: [{
      data: Object.values(effectCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

const treatmentAdherenceChartData = computed(() => {
  if (!statsData.value?.canary_analysis_files) return { labels: [], datasets: [] }
  
  const adherences = statsData.value.canary_analysis_files.map((file: any) => file.canary_data.analysis.treatment_adherence)
  const adherenceCounts = adherences.reduce((acc: any, adherence: string) => {
    acc[adherence] = (acc[adherence] || 0) + 1
    return acc
  }, {})
  
  return {
    labels: Object.keys(adherenceCounts),
    datasets: [{
      data: Object.values(adherenceCounts),
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
    }]
  }
})

// Helper functions for trend indicators
const getCognitiveTrend = () => {
  const score = cognitiveHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getCognitiveTrendClass = () => {
  const score = cognitiveHealthScore.value
  if (score >= 8) return 'positive'
  if (score >= 6) return 'neutral'
  return 'negative'
}

const getMentalHealthTrend = () => {
  const score = mentalHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getMentalHealthTrendClass = () => {
  const score = mentalHealthScore.value
  if (score >= 8) return 'positive'
  if (score >= 6) return 'neutral'
  return 'negative'
}

const getPhysicalHealthTrend = () => {
  const score = physicalHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getPhysicalHealthTrendClass = () => {
  const score = physicalHealthScore.value
  if (score >= 8) return 'positive'
  if (score >= 6) return 'neutral'
  return 'negative'
}

const getSocialHealthTrend = () => {
  const score = socialHealthScore.value
  if (score >= 8) return 'Excellent'
  if (score >= 6) return 'Good'
  if (score >= 4) return 'Fair'
  return 'Poor'
}

const getSocialHealthTrendClass = () => {
  const score = socialHealthScore.value
  if (score >= 8) return 'positive'
  if (score >= 6) return 'neutral'
  return 'negative'
}

const getCallTrend = () => {
  const calls = totalCalls.value
  if (calls >= 15) return 'Active'
  if (calls >= 10) return 'Moderate'
  return 'Low'
}

// Risk assessment helper functions
const getRiskLevel = (score: number) => {
  if (score <= 3) return 'low'
  if (score <= 6) return 'moderate'
  return 'high'
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
      console.log('📊 Loaded enhanced canary analysis data:', result.data)
    } else {
      throw new Error(result.message || 'Failed to load enhanced canary data')
    }
  } catch (err) {
    console.error('Error loading enhanced stats:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load enhanced stats'
    
    // Fallback to basic data structure
    statsData.value = {
      aggregated_health_summary: {
        total_calls: 0,
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
        monitored_conditions_count: 0
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
.enhanced-stats-dashboard {
  padding: 16px;
  background: transparent;
}

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  text-align: center;
}

.dashboard-content {
  max-width: 1400px;
  margin: 0 auto;
}

.metrics-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 32px;
}

.metric-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.metric-icon {
  font-size: 24px;
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  background: #f3f4f6;
}

.metric-content {
  flex: 1;
}

.metric-label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 4px;
}

.metric-value {
  font-size: 24px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 4px;
}

.metric-trend {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 12px;
}

.metric-trend.positive {
  background: #dcfce7;
  color: #166534;
}

.metric-trend.neutral {
  background: #fef3c7;
  color: #92400e;
}

.metric-trend.negative {
  background: #fee2e2;
  color: #991b1b;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.risk-assessment-section {
  margin-bottom: 32px;
}

.risk-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.risk-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.risk-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.risk-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.risk-icon {
  font-size: 18px;
}

.risk-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.risk-score {
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 8px;
}

.risk-score.low {
  color: #10b981;
}

.risk-score.moderate {
  color: #f59e0b;
}

.risk-score.high {
  color: #ef4444;
}

.risk-indicator {
  width: 100%;
  height: 4px;
  border-radius: 2px;
  margin-bottom: 8px;
}

.risk-indicator.low {
  background: #10b981;
}

.risk-indicator.moderate {
  background: #f59e0b;
}

.risk-indicator.high {
  background: #ef4444;
}

.risk-description {
  font-size: 12px;
  color: #6b7280;
  line-height: 1.4;
}

.charts-section {
  margin-bottom: 32px;
}

.charts-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.chart-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
}

.chart-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: transform 0.2s, box-shadow 0.2s;
}

.chart-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.chart-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 16px;
  text-align: center;
}

.chart-container {
  height: 200px;
  position: relative;
}

.health-conditions-section {
  margin-bottom: 32px;
}

.conditions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.condition-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  text-align: center;
  transition: transform 0.2s, box-shadow 0.2s;
}

.condition-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.condition-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 12px;
}

.condition-icon {
  font-size: 18px;
}

.condition-title {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.condition-count {
  font-size: 32px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
}

.condition-description {
  font-size: 12px;
  color: #6b7280;
}

.last-updated {
  text-align: center;
  padding: 20px;
  background: #f9fafb;
  border-radius: 8px;
  margin-top: 20px;
}
</style>
