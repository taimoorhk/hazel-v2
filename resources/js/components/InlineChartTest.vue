<template>
  <div class="inline-chart-test">
    <h2>Inline Chart Test</h2>
    <p>Testing Chart.js directly...</p>
    
    <div class="chart-container" style="width: 400px; height: 300px; border: 2px solid red;">
      <canvas ref="chartCanvas" width="400" height="300"></canvas>
    </div>
    
    <div class="debug-info">
      <p>Chart instance: {{ chartInstance ? 'Created' : 'Not created' }}</p>
      <p>Canvas element: {{ chartCanvas ? 'Found' : 'Not found' }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
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

const chartCanvas = ref<HTMLCanvasElement | null>(null)
const chartInstance = ref<ChartJS | null>(null)

onMounted(() => {
  console.log('🎯 InlineChartTest mounted')
  console.log('🎯 Chart.js available:', !!ChartJS)
  console.log('🎯 Canvas ref:', chartCanvas.value)
  
  if (chartCanvas.value) {
    try {
      chartInstance.value = new ChartJS(chartCanvas.value, {
        type: 'doughnut',
        data: {
          labels: ['Red', 'Blue', 'Yellow'],
          datasets: [{
            data: [300, 50, 100],
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
          }]
        },
        options: {
          responsive: false,
          maintainAspectRatio: false
        }
      })
      console.log('🎯 Chart created successfully:', chartInstance.value)
    } catch (error) {
      console.error('🎯 Chart creation failed:', error)
    }
  } else {
    console.error('🎯 Canvas element not found')
  }
})
</script>

<style scoped>
.inline-chart-test {
  padding: 20px;
  max-width: 600px;
  margin: 0 auto;
}

.chart-container {
  margin: 20px 0;
  background: #f9f9f9;
}

.debug-info {
  margin: 20px 0;
  padding: 20px;
  background: #e9ecef;
  border-radius: 8px;
}
</style>
