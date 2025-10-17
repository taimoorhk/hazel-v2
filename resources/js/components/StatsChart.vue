<template>
  <div class="stats-chart-container">
    <div class="chart-header">
      <h3 class="chart-title">{{ title }}</h3>
      <p class="chart-subtitle" v-if="subtitle">{{ subtitle }}</p>
    </div>
    
    <div class="chart-wrapper" :style="{ height: height + 'px' }">
      <canvas ref="chartCanvas"></canvas>
    </div>
    
    <div class="chart-footer" v-if="showFooter">
      <div class="chart-legend" v-if="legend">
        <div 
          v-for="(item, index) in legend" 
          :key="index"
          class="legend-item"
        >
          <div 
            class="legend-color" 
            :style="{ backgroundColor: item.color }"
          ></div>
          <span class="legend-label">{{ item.label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

interface Props {
  type: 'bar' | 'line' | 'doughnut' | 'pie'
  data: any
  options?: any
  title?: string
  subtitle?: string
  height?: number
  showFooter?: boolean
  legend?: Array<{ label: string; color: string }>
}

const props = withDefaults(defineProps<Props>(), {
  height: 300,
  showFooter: false,
  title: '',
  subtitle: '',
  legend: () => []
})

const chartCanvas = ref<HTMLCanvasElement | null>(null)
const chartInstance = ref<ChartJS | null>(null)

const createChart = () => {
  if (!chartCanvas.value) return

  // Destroy existing chart
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }

  const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        backgroundColor: 'rgba(0, 0, 0, 0.8)',
        titleColor: 'white',
        bodyColor: 'white',
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
        cornerRadius: 8,
        padding: 12
      }
    },
    scales: props.type !== 'doughnut' && props.type !== 'pie' ? {
      x: {
        grid: {
          color: 'rgba(0, 0, 0, 0.05)',
          borderColor: 'rgba(0, 0, 0, 0.1)'
        },
        ticks: {
          color: 'rgba(0, 0, 0, 0.7)'
        }
      },
      y: {
        grid: {
          color: 'rgba(0, 0, 0, 0.05)',
          borderColor: 'rgba(0, 0, 0, 0.1)'
        },
        ticks: {
          color: 'rgba(0, 0, 0, 0.7)'
        },
        beginAtZero: true
      }
    } : undefined
  }

  const chartOptions = { ...defaultOptions, ...props.options }

  chartInstance.value = new ChartJS(chartCanvas.value, {
    type: props.type,
    data: props.data,
    options: chartOptions
  })
}

onMounted(() => {
  nextTick(() => {
    createChart()
  })
})

onUnmounted(() => {
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }
})

watch(() => props.data, () => {
  createChart()
}, { deep: true })

watch(() => props.options, () => {
  createChart()
}, { deep: true })
</script>

<style scoped>
.stats-chart-container {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-header {
  margin-bottom: 20px;
}

.chart-title {
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.chart-subtitle {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.chart-wrapper {
  position: relative;
  width: 100%;
}

.chart-footer {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}

.legend-label {
  font-size: 14px;
  color: #6b7280;
}
</style>
