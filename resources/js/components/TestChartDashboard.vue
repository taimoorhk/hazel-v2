<template>
  <div class="test-chart-dashboard">
    <h2>Test Chart Dashboard</h2>
    
    <!-- Simple test chart -->
    <div class="test-chart-container">
      <h3>Test Mood Chart</h3>
      <div class="chart-wrapper" style="height: 300px; width: 400px; border: 1px solid #ccc;">
        <SimpleChart
          :data="testMoodData"
          type="doughnut"
          height="300"
        />
      </div>
    </div>

    <!-- Debug info -->
    <div class="debug-info">
      <h3>Debug Information</h3>
      <pre>{{ JSON.stringify(testMoodData, null, 2) }}</pre>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import SimpleChart from '@/components/SimpleChart.vue'

// Simple test data
const testMoodData = ref({
  labels: ['Happy', 'Neutral', 'Sad'],
  datasets: [{
    data: [3, 2, 1],
    backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
  }]
})
</script>

<style scoped>
.test-chart-dashboard {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.test-chart-container {
  margin: 20px 0;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
}

.chart-wrapper {
  margin: 20px 0;
}

.debug-info {
  margin: 20px 0;
  padding: 20px;
  background: #f5f5f5;
  border-radius: 8px;
}

pre {
  background: white;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
}
</style>
