<template>
  <div class="simple-chart-wrapper" :style="{ height: height + 'px' }">
    <canvas ref="chartCanvas"></canvas>
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
  height?: number
}

const props = withDefaults(defineProps<Props>(), {
  height: 180
})

const chartCanvas = ref<HTMLCanvasElement | null>(null)
const chartInstance = ref<ChartJS | null>(null)

const createChart = () => {
  if (!chartCanvas.value || !props.data) return

  // Destroy existing chart
  if (chartInstance.value) {
    chartInstance.value.destroy()
  }

  const defaultOptions = {
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
          color: 'rgba(0, 0, 0, 0.7)',
          font: {
            size: 11
          }
        }
      },
      y: {
        grid: {
          color: 'rgba(0, 0, 0, 0.05)',
          borderColor: 'rgba(0, 0, 0, 0.1)'
        },
        ticks: {
          color: 'rgba(0, 0, 0, 0.7)',
          font: {
            size: 11
          }
        },
        beginAtZero: true
      }
    } : undefined
  }

  const chartOptions = { ...defaultOptions, ...props.options }

  try {
    chartInstance.value = new ChartJS(chartCanvas.value, {
      type: props.type,
      data: props.data,
      options: chartOptions
    })
  } catch (error) {
    console.error('Chart creation error:', error)
  }
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
  nextTick(() => {
    createChart()
  })
}, { deep: true })

watch(() => props.options, () => {
  nextTick(() => {
    createChart()
  })
}, { deep: true })
</script>

<style scoped>
.simple-chart-wrapper {
  position: relative;
  width: 100%;
}
</style>
