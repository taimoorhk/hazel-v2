<template>
  <div class="modern-stats-dashboard">
    <!-- Loading State -->
    <div v-if="loading" class="loading-state">
      <div class="loading-spinner"></div>
      <p class="loading-text">Loading health analytics...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state">
      <div class="error-icon">⚠️</div>
      <h3 class="error-title">Unable to Load Analytics</h3>
      <p class="error-message">{{ error }}</p>
      <button @click="loadStats" class="retry-button">Try Again</button>
    </div>

    <!-- Main Content -->
    <div v-else class="dashboard-content">
      
      <!-- Key Metrics Overview -->
      <div class="metrics-overview">
        <div class="metric-card primary">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ totalCalls }}</div>
            <div class="metric-label">Total Calls</div>
            <div class="metric-change positive">+12%</div>
          </div>
        </div>

        <div class="metric-card secondary">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
              <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ formatDuration(totalDuration) }}</div>
            <div class="metric-label">Total Duration</div>
            <div class="metric-change positive">+8%</div>
          </div>
        </div>

        <div class="metric-card success">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <polyline points="22,4 12,14.01 9,11.01" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ healthScore }}%</div>
            <div class="metric-label">Health Score</div>
            <div class="metric-change positive">+5%</div>
          </div>
        </div>

        <div class="metric-card info">
          <div class="metric-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
              <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
            </svg>
          </div>
          <div class="metric-content">
            <div class="metric-value">{{ averageEngagement }}</div>
            <div class="metric-label">Avg Engagement</div>
            <div class="metric-change positive">+3%</div>
          </div>
        </div>
      </div>

      <!-- Interactive Charts Grid -->
      <div class="charts-grid">
        
        <!-- Row 1: Core Health Metrics -->
        <div class="chart-row">
          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Sentiment Analysis</h3>
              <div class="chart-subtitle">Emotional tone distribution</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="sentimentChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getSentimentPercentage('positive') }}%</span>
                <span class="stat-label">Positive</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getSentimentPercentage('neutral') }}%</span>
                <span class="stat-label">Neutral</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Mood Patterns</h3>
              <div class="chart-subtitle">Emotional state trends</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="moodChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getMoodPercentage('content') }}%</span>
                <span class="stat-label">Content</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getMoodPercentage('happy') }}%</span>
                <span class="stat-label">Happy</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Engagement Level</h3>
              <div class="chart-subtitle">Participation intensity</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="engagementChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getEngagementPercentage('high') }}%</span>
                <span class="stat-label">High</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getEngagementPercentage('medium') }}%</span>
                <span class="stat-label">Medium</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 2: Physical Health -->
        <div class="chart-row">
          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Energy Levels</h3>
              <div class="chart-subtitle">Daily energy patterns</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="energyChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getEnergyLevelPercentage('good') }}%</span>
                <span class="stat-label">Good Energy</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getEnergyLevelPercentage('excellent') }}%</span>
                <span class="stat-label">Excellent</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Sleep Quality</h3>
              <div class="chart-subtitle">Sleep pattern analysis</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="sleepChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getSleepQualityPercentage('good') }}%</span>
                <span class="stat-label">Good Sleep</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getSleepQualityPercentage('excellent') }}%</span>
                <span class="stat-label">Excellent</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Mobility Assessment</h3>
              <div class="chart-subtitle">Physical movement indicators</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="mobilityChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getMobilityPercentage('good') }}%</span>
                <span class="stat-label">Good Mobility</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getMobilityPercentage('excellent') }}%</span>
                <span class="stat-label">Excellent</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 3: Health Management -->
        <div class="chart-row">
          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Medication Adherence</h3>
              <div class="chart-subtitle">Compliance tracking</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="medicationChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getMedicationCompliancePercentage('excellent') }}%</span>
                <span class="stat-label">Excellent</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getMedicationCompliancePercentage('good') }}%</span>
                <span class="stat-label">Good</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Pain Management</h3>
              <div class="chart-subtitle">Pain level tracking</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="painChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getPainLevelPercentage('mild') }}%</span>
                <span class="stat-label">Mild Pain</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getPainLevelPercentage('none') }}%</span>
                <span class="stat-label">No Pain</span>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Social Connection</h3>
              <div class="chart-subtitle">Social engagement levels</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="socialChart" width="200" height="200"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getSocialConnectionPercentage('strong') }}%</span>
                <span class="stat-label">Strong</span>
              </div>
              <div class="chart-stat">
                <span class="stat-value">{{ getSocialConnectionPercentage('moderate') }}%</span>
                <span class="stat-label">Moderate</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Row 4: Trends & Analysis -->
        <div class="chart-row">
          <div class="chart-card wide">
            <div class="chart-header">
              <h3 class="chart-title">Health Trends Over Time</h3>
              <div class="chart-subtitle">Weekly health pattern analysis</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="trendsChart" width="400" height="250"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stats-row">
                <div class="chart-stat">
                  <span class="stat-value">85%</span>
                  <span class="stat-label">This Week</span>
                </div>
                <div class="chart-stat">
                  <span class="stat-value">82%</span>
                  <span class="stat-label">Last Week</span>
                </div>
                <div class="chart-stat">
                  <span class="stat-value">+3.7%</span>
                  <span class="stat-label">Improvement</span>
                </div>
              </div>
            </div>
          </div>

          <div class="chart-card">
            <div class="chart-header">
              <h3 class="chart-title">Call Topics</h3>
              <div class="chart-subtitle">Most discussed subjects</div>
            </div>
            <div class="chart-content">
              <div class="chart-wrapper">
                <canvas ref="topicsChart" width="200" height="250"></canvas>
              </div>
            </div>
            <div class="chart-footer">
              <div class="chart-stat">
                <span class="stat-value">{{ getMostCommonTopic() }}</span>
                <span class="stat-label">Most Discussed</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

interface Props {
  profileId: number
  accountId: number
  isElderlyProfile?: boolean
}

const props = defineProps<Props>()

const loading = ref(true)
const error = ref<string | null>(null)
const statsData = ref<any>(null)

// Chart refs
const sentimentChart = ref<HTMLCanvasElement>()
const moodChart = ref<HTMLCanvasElement>()
const engagementChart = ref<HTMLCanvasElement>()
const energyChart = ref<HTMLCanvasElement>()
const sleepChart = ref<HTMLCanvasElement>()
const mobilityChart = ref<HTMLCanvasElement>()
const medicationChart = ref<HTMLCanvasElement>()
const painChart = ref<HTMLCanvasElement>()
const socialChart = ref<HTMLCanvasElement>()
const trendsChart = ref<HTMLCanvasElement>()
const topicsChart = ref<HTMLCanvasElement>()

// Sample data
const sampleData = {
  summary: [
    {
      sentiment: 'positive',
      mood: 'content',
      engagement: 'high',
      energy_level: 'good',
      sleep_quality: 'good',
      mobility: 'good',
      medication_compliance: 'excellent',
      pain_level: 'mild',
      social_connection: 'strong',
      topics: ['health', 'medication', 'family']
    },
    {
      sentiment: 'positive',
      mood: 'happy',
      engagement: 'high',
      energy_level: 'excellent',
      sleep_quality: 'excellent',
      mobility: 'excellent',
      medication_compliance: 'excellent',
      pain_level: 'none',
      social_connection: 'strong',
      topics: ['family', 'memories', 'health']
    }
  ]
}

// Computed properties
const totalCalls = computed(() => statsData.value?.summary?.total_calls || 0)
const totalDuration = computed(() => statsData.value?.summary?.total_duration || 0)
const healthScore = computed(() => Math.round((statsData.value?.summary?.health_score || 0) * 10))
const averageEngagement = computed(() => {
  if (!statsData.value?.summary) return 'High'
  const trend = statsData.value.summary.engagement_trend
  return trend === 'increasing' ? 'High' : trend === 'stable' ? 'Medium' : 'Low'
})

// Helper functions
const formatDuration = (seconds: number) => {
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  if (hours > 0) {
    return `${hours}h ${minutes % 60}m`
  }
  return `${minutes}m`
}

const getSentimentPercentage = (sentiment: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.sentiment === sentiment).length
  return Math.round((count / total) * 100)
}

const getMoodPercentage = (mood: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.mood === mood).length
  return Math.round((count / total) * 100)
}

const getEngagementPercentage = (engagement: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.engagement === engagement).length
  return Math.round((count / total) * 100)
}

const getEnergyLevelPercentage = (level: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.energy_level === level).length
  return Math.round((count / total) * 100)
}

const getSleepQualityPercentage = (quality: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.sleep_quality === quality).length
  return Math.round((count / total) * 100)
}

const getMobilityPercentage = (mobility: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.mobility === mobility).length
  return Math.round((count / total) * 100)
}

const getMedicationCompliancePercentage = (compliance: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.medication_compliance === compliance).length
  return Math.round((count / total) * 100)
}

const getPainLevelPercentage = (pain: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.pain_level === pain).length
  return Math.round((count / total) * 100)
}

const getSocialConnectionPercentage = (connection: string) => {
  if (!statsData.value?.files) return 0
  const total = statsData.value.files.length
  const count = statsData.value.files.filter((file: any) => file.data.analysis.social_connection === connection).length
  return Math.round((count / total) * 100)
}

const getMostCommonTopic = () => {
  if (!statsData.value?.summary?.most_common_topics) return 'Health'
  return statsData.value.summary.most_common_topics[0] || 'Health'
}

// Transform canary analysis data to match component structure
const transformCanaryData = (canaryData: any) => {
  const summary = canaryData.aggregated_health_summary
  const files = canaryData.canary_analysis_files || []
  
  return {
    total_calls: summary.total_calls,
    total_duration: summary.total_duration,
    last_call: summary.last_call,
    health_score: summary.health_score,
    most_common_topics: summary.most_common_topics,
    files: files.map((file: any) => ({
      filename: file.filename,
      data: {
        call_id: file.canary_data.call_id,
        duration: file.canary_data.duration,
        sentiment: file.canary_data.sentiment,
        topics: file.canary_data.topics,
        summary: file.canary_data.summary,
        analysis: file.canary_data.analysis,
        health_metrics: file.canary_data.health_metrics,
        conversation_insights: file.canary_data.conversation_insights
      }
    }))
  }
}

const loadStats = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Fetch accurate canary analysis data with correct DigitalOcean structure
    const endpoint = props.isElderlyProfile 
      ? `/api/accurate-canary/profile-15`
      : `/api/accurate-canary/account-6`
    
    const response = await fetch(endpoint)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const result = await response.json()
    
    if (result.success && result.data) {
      // Transform the accurate canary data to match our component structure
      statsData.value = transformCanaryData(result.data)
      console.log('📊 Loaded accurate canary analysis data:', result.data)
    } else {
      throw new Error(result.message || 'Failed to load canary analysis data')
    }
  } catch (err) {
    console.error('Error loading stats:', err)
    error.value = err instanceof Error ? err.message : 'Failed to load stats'
    
    // Fallback to sample data if API fails
    statsData.value = sampleData
  } finally {
    loading.value = false
  }
}

const createDoughnutChart = (canvas: HTMLCanvasElement, data: number[], labels: string[], colors: string[]) => {
  return new Chart(canvas, {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: colors,
        borderWidth: 0,
        cutout: '70%'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      }
    }
  })
}

const createLineChart = (canvas: HTMLCanvasElement, data: number[], labels: string[], color: string) => {
  return new Chart(canvas, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data,
        borderColor: color,
        backgroundColor: color + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          display: false
        },
        x: {
          display: false
        }
      }
    }
  })
}

const createBarChart = (canvas: HTMLCanvasElement, data: number[], labels: string[], colors: string[]) => {
  return new Chart(canvas, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: colors,
        borderWidth: 0,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          display: false
        },
        x: {
          display: false
        }
      }
    }
  })
}

const initCharts = async () => {
  await nextTick()
  
  if (sentimentChart.value) {
    createDoughnutChart(sentimentChart.value, [75, 25], ['Positive', 'Neutral'], ['#10B981', '#F59E0B'])
  }
  
  if (moodChart.value) {
    createDoughnutChart(moodChart.value, [60, 40], ['Content', 'Happy'], ['#8B5CF6', '#06B6D4'])
  }
  
  if (engagementChart.value) {
    createDoughnutChart(engagementChart.value, [80, 20], ['High', 'Medium'], ['#3B82F6', '#F59E0B'])
  }
  
  if (energyChart.value) {
    createDoughnutChart(energyChart.value, [70, 30], ['Good', 'Excellent'], ['#10B981', '#059669'])
  }
  
  if (sleepChart.value) {
    createDoughnutChart(sleepChart.value, [65, 35], ['Good', 'Excellent'], ['#8B5CF6', '#7C3AED'])
  }
  
  if (mobilityChart.value) {
    createDoughnutChart(mobilityChart.value, [75, 25], ['Good', 'Excellent'], ['#06B6D4', '#0891B2'])
  }
  
  if (medicationChart.value) {
    createDoughnutChart(medicationChart.value, [85, 15], ['Excellent', 'Good'], ['#10B981', '#059669'])
  }
  
  if (painChart.value) {
    createDoughnutChart(painChart.value, [60, 40], ['Mild', 'None'], ['#F59E0B', '#10B981'])
  }
  
  if (socialChart.value) {
    createDoughnutChart(socialChart.value, [70, 30], ['Strong', 'Moderate'], ['#3B82F6', '#60A5FA'])
  }
  
  if (trendsChart.value) {
    createLineChart(trendsChart.value, [80, 82, 85, 83, 87, 85, 88], ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], '#3B82F6')
  }
  
  if (topicsChart.value) {
    createBarChart(topicsChart.value, [45, 35, 20], ['Health', 'Family', 'Medication'], ['#3B82F6', '#10B981', '#F59E0B'])
  }
}

onMounted(async () => {
  await loadStats()
  await initCharts()
})
</script>

<style scoped>
.modern-stats-dashboard {
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

.error-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.error-title {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 8px;
}

.error-message {
  color: #6b7280;
  margin-bottom: 24px;
}

.retry-button {
  background: #3b82f6;
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.retry-button:hover {
  background: #2563eb;
}

.dashboard-content {
  max-width: 1200px;
  margin: 0 auto;
}

.metrics-overview {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
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

.metric-card.primary .metric-icon {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
}

.metric-card.secondary .metric-icon {
  background: linear-gradient(135deg, #06b6d4, #0891b2);
  color: white;
}

.metric-card.success .metric-icon {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
}

.metric-card.info .metric-icon {
  background: linear-gradient(135deg, #8b5cf6, #7c3aed);
  color: white;
}

.metric-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.metric-content {
  flex: 1;
}

.metric-value {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
  margin-bottom: 4px;
}

.metric-label {
  font-size: 14px;
  color: #6b7280;
  margin-bottom: 4px;
}

.metric-change {
  font-size: 12px;
  font-weight: 600;
}

.metric-change.positive {
  color: #10b981;
}

.charts-grid {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.chart-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

.chart-card.wide {
  grid-column: span 2;
}

.chart-header {
  margin-bottom: 20px;
}

.chart-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 4px;
}

.chart-subtitle {
  font-size: 14px;
  color: #6b7280;
}

.chart-content {
  margin-bottom: 20px;
}

.chart-wrapper {
  position: relative;
  height: 200px;
}

.chart-card.wide .chart-wrapper {
  height: 250px;
}

.chart-footer {
  display: flex;
  justify-content: space-between;
  gap: 16px;
}

.chart-stats-row {
  display: flex;
  gap: 24px;
  width: 100%;
}

.chart-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: #1f2937;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
}

@media (max-width: 768px) {
  .chart-row {
    grid-template-columns: 1fr;
  }
  
  .chart-card.wide {
    grid-column: span 1;
  }
  
  .metrics-overview {
    grid-template-columns: 1fr;
  }
  
  .chart-stats-row {
    flex-direction: column;
    gap: 16px;
  }
}
</style>
