<template>
  <div class="final-health-data-cards">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p class="loading-text">Loading final health data...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h3 class="error-title">Unable to Load Data</h3>
      <p class="error-message">{{ error }}</p>
      <button @click="fetchData" class="retry-button">Retry</button>
    </div>

    <!-- No Data State -->
    <div v-else-if="!hasData" class="no-data-container">
      <div class="no-data-icon">📊</div>
      <h3 class="no-data-title">Weekly Report Pending</h3>
      <p class="no-data-message">Final health data is not available for this user at the moment.</p>
    </div>

    <!-- Data Available -->
    <div v-else class="data-container">
      <!-- Overall Health Score -->
      <div class="health-score-card">
        <div class="card-header">
          <div class="card-icon">🏥</div>
          <h3 class="card-title">Overall Health Score</h3>
        </div>
        <div class="score-display">
          <div class="score-value">{{ overallHealthScore }}/10</div>
          <div class="score-bar">
            <div class="score-fill" :style="{ width: (overallHealthScore / 10) * 100 + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Individual Health Scores -->
      <div class="scores-grid">
        <div class="score-card">
          <div class="score-label">Cognitive Health</div>
          <div class="score-value">{{ cognitiveHealthScore }}/10</div>
        </div>
        <div class="score-card">
          <div class="score-label">Mental Health</div>
          <div class="score-value">{{ mentalHealthScore }}/10</div>
        </div>
        <div class="score-card">
          <div class="score-label">Physical Health</div>
          <div class="score-value">{{ physicalHealthScore }}/10</div>
        </div>
        <div class="score-card">
          <div class="score-label">Social Health</div>
          <div class="score-value">{{ socialHealthScore }}/10</div>
        </div>
      </div>

      <!-- Risk Assessment -->
      <div class="risk-assessment">
        <h3 class="section-title">Risk Assessment</h3>
        <div class="risk-grid">
          <div class="risk-card" :class="getRiskClass('alzheimer')">
            <div class="risk-label">Alzheimer's Risk</div>
            <div class="risk-value">{{ alzheimerRisk }}/10</div>
            <div class="risk-status">{{ getRiskStatus('alzheimer') }}</div>
          </div>
          <div class="risk-card" :class="getRiskClass('parkinson')">
            <div class="risk-label">Parkinson's Risk</div>
            <div class="risk-value">{{ parkinsonRisk }}/10</div>
            <div class="risk-status">{{ getRiskStatus('parkinson') }}</div>
          </div>
          <div class="risk-card" :class="getRiskClass('depression')">
            <div class="risk-label">Depression Risk</div>
            <div class="risk-value">{{ depressionRisk }}/10</div>
            <div class="risk-status">{{ getRiskStatus('depression') }}</div>
          </div>
          <div class="risk-card" :class="getRiskClass('anxiety')">
            <div class="risk-label">Anxiety Risk</div>
            <div class="risk-value">{{ anxietyRisk }}/10</div>
            <div class="risk-status">{{ getRiskStatus('anxiety') }}</div>
          </div>
        </div>
      </div>

      <!-- Trends -->
      <div class="trends-section">
        <h3 class="section-title">Health Trends</h3>
        <div class="trends-grid">
          <div class="trend-card">
            <div class="trend-label">Improvement</div>
            <div class="trend-value positive">+{{ improvementRate }}%</div>
          </div>
          <div class="trend-card">
            <div class="trend-label">Stability</div>
            <div class="trend-value">{{ stabilityRate }}%</div>
          </div>
          <div class="trend-card">
            <div class="trend-label">Concern</div>
            <div class="trend-value negative">{{ concernRate }}%</div>
          </div>
        </div>
      </div>

      <!-- Data Quality -->
      <div class="data-quality">
        <h3 class="section-title">Data Quality</h3>
        <div class="quality-metrics">
          <div class="quality-item">
            <span class="quality-label">Data Completeness</span>
            <span class="quality-value">{{ dataCompleteness }}%</span>
          </div>
          <div class="quality-item">
            <span class="quality-label">Last Updated</span>
            <span class="quality-value">{{ lastUpdated }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { finalHealthDataApi } from '@/lib/finalHealthDataApi'

interface Props {
  accountId: number
  profileId: number
  autoRefresh?: boolean
  refreshInterval?: number
}

const props = withDefaults(defineProps<Props>(), {
  autoRefresh: true,
  refreshInterval: 15000
})

// Reactive state
const loading = ref(true)
const error = ref<string | null>(null)
const healthData = ref<any>(null)
const refreshTimer = ref<number | null>(null)

// Computed properties
const hasData = computed(() => healthData.value && healthData.value.has_data)

const overallHealthScore = computed(() => {
  if (!hasData.value) return 0
  return Math.round((healthData.value.aggregated_health_summary?.overall_health_score || 0) * 10) / 10
})

const cognitiveHealthScore = computed(() => {
  if (!hasData.value) return 0
  return Math.round((healthData.value.aggregated_health_summary?.cognitive_health_score || 0) * 10) / 10
})

const mentalHealthScore = computed(() => {
  if (!hasData.value) return 0
  return Math.round((healthData.value.aggregated_health_summary?.mental_health_score || 0) * 10) / 10
})

const physicalHealthScore = computed(() => {
  if (!hasData.value) return 0
  return Math.round((healthData.value.aggregated_health_summary?.physical_health_score || 0) * 10) / 10
})

const socialHealthScore = computed(() => {
  if (!hasData.value) return 0
  return Math.round((healthData.value.aggregated_health_summary?.social_health_score || 0) * 10) / 10
})

const alzheimerRisk = computed(() => {
  if (!hasData.value) return 0
  return healthData.value.aggregated_health_summary?.alzheimer_risk_score || 0
})

const parkinsonRisk = computed(() => {
  if (!hasData.value) return 0
  return healthData.value.aggregated_health_summary?.parkinson_risk_score || 0
})

const depressionRisk = computed(() => {
  if (!hasData.value) return 0
  return healthData.value.aggregated_health_summary?.depression_risk_score || 0
})

const anxietyRisk = computed(() => {
  if (!hasData.value) return 0
  return healthData.value.aggregated_health_summary?.anxiety_risk_score || 0
})

const improvementRate = computed(() => {
  if (!hasData.value) return 0
  return Math.round(Math.random() * 20 + 5) // Mock data
})

const stabilityRate = computed(() => {
  if (!hasData.value) return 0
  return Math.round(Math.random() * 30 + 60) // Mock data
})

const concernRate = computed(() => {
  if (!hasData.value) return 0
  return Math.round(Math.random() * 15 + 5) // Mock data
})

const dataCompleteness = computed(() => {
  if (!hasData.value) return 0
  return Math.round(Math.random() * 20 + 80) // Mock data
})

const lastUpdated = computed(() => {
  if (!hasData.value) return 'N/A'
  return new Date().toLocaleDateString()
})

// Methods
const fetchData = async () => {
  try {
    loading.value = true
    error.value = null
    
    const response = await finalHealthDataApi.getFinalHealthData(props.accountId, props.profileId)
    
    if (response.success && response.data) {
      healthData.value = response.data
    } else {
      healthData.value = null
    }
  } catch (err) {
    error.value = 'Failed to fetch health data'
    healthData.value = null
  } finally {
    loading.value = false
  }
}

const getRiskClass = (riskType: string) => {
  const risk = getRiskValue(riskType)
  if (risk >= 7) return 'risk-high'
  if (risk >= 4) return 'risk-moderate'
  return 'risk-low'
}

const getRiskStatus = (riskType: string) => {
  const risk = getRiskValue(riskType)
  if (risk >= 7) return 'High Risk'
  if (risk >= 4) return 'Moderate Risk'
  return 'Low Risk'
}

const getRiskValue = (riskType: string) => {
  switch (riskType) {
    case 'alzheimer': return alzheimerRisk.value
    case 'parkinson': return parkinsonRisk.value
    case 'depression': return depressionRisk.value
    case 'anxiety': return anxietyRisk.value
    default: return 0
  }
}

const startAutoRefresh = () => {
  if (props.autoRefresh && props.refreshInterval > 0) {
    refreshTimer.value = window.setInterval(fetchData, props.refreshInterval)
  }
}

const stopAutoRefresh = () => {
  if (refreshTimer.value) {
    clearInterval(refreshTimer.value)
    refreshTimer.value = null
  }
}

// Lifecycle
onMounted(() => {
  fetchData()
  startAutoRefresh()
})

onUnmounted(() => {
  stopAutoRefresh()
})
</script>

<style scoped>
.final-health-data-cards {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px;
}

/* Loading and Error States */
.loading-container,
.error-container,
.no-data-container {
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
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text,
.error-title,
.no-data-title {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.error-message,
.no-data-message {
  color: #6b7280;
  font-size: 14px;
  margin-bottom: 16px;
}

.retry-button {
  background-color: #3b82f6;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-size: 14px;
}

.retry-button:hover {
  background-color: #2563eb;
}

/* Data Container */
.data-container {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Health Score Card */
.health-score-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.card-icon {
  font-size: 24px;
}

.card-title {
  font-size: 20px;
  font-weight: 600;
  color: #374151;
}

.score-display {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.score-value {
  font-size: 36px;
  font-weight: 700;
  color: #1f2937;
}

.score-bar {
  width: 100%;
  height: 8px;
  background-color: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.score-fill {
  height: 100%;
  background: linear-gradient(90deg, #ef4444 0%, #f59e0b 50%, #10b981 100%);
  transition: width 0.3s ease;
}

/* Scores Grid */
.scores-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.score-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  text-align: center;
}

.score-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.score-value {
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
}

/* Risk Assessment */
.risk-assessment {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 16px;
}

.risk-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
}

.risk-card {
  border-radius: 8px;
  padding: 16px;
  text-align: center;
  border: 2px solid;
}

.risk-card.risk-low {
  border-color: #10b981;
  background-color: #f0fdf4;
}

.risk-card.risk-moderate {
  border-color: #f59e0b;
  background-color: #fffbeb;
}

.risk-card.risk-high {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.risk-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.risk-value {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 4px;
}

.risk-status {
  font-size: 12px;
  font-weight: 500;
  padding: 2px 8px;
  border-radius: 4px;
}

.risk-card.risk-low .risk-status {
  background-color: #10b981;
  color: white;
}

.risk-card.risk-moderate .risk-status {
  background-color: #f59e0b;
  color: white;
}

.risk-card.risk-high .risk-status {
  background-color: #ef4444;
  color: white;
}

/* Trends Section */
.trends-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.trends-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
}

.trend-card {
  text-align: center;
  padding: 16px;
  border-radius: 8px;
  background-color: #f9fafb;
}

.trend-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 8px;
}

.trend-value {
  font-size: 20px;
  font-weight: 600;
}

.trend-value.positive {
  color: #10b981;
}

.trend-value.negative {
  color: #ef4444;
}

/* Data Quality */
.data-quality {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.quality-metrics {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.quality-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f3f4f6;
}

.quality-item:last-child {
  border-bottom: none;
}

.quality-label {
  font-size: 14px;
  color: #6b7280;
}

.quality-value {
  font-size: 14px;
  font-weight: 600;
  color: #1f2937;
}

/* Responsive Design */
@media (max-width: 768px) {
  .final-health-data-cards {
    padding: 16px;
  }
  
  .scores-grid,
  .risk-grid,
  .trends-grid {
    grid-template-columns: 1fr;
  }
}
</style>
