<template>
  <div class="min-h-screen p-8" style="background-color: #f9fafb;">
    <div class="max-w-6xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Weekly Summary Test</h1>
      
      <!-- Test Account 7 (microassetsmain@gmail.com) -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account 7 - microassetsmain@gmail.com</h2>
        <WeeklySummaryCards 
          :account-id="7" 
          :profile-id="-1" 
          :time-range-weeks="1"
          :auto-refresh="false"
          :use-mock-data="true"
        />
      </div>

      <!-- Test Account 6 (mtaimoorhas1@gmail.com) -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account 6 - mtaimoorhas1@gmail.com</h2>
        <WeeklySummaryCards 
          :account-id="6" 
          :profile-id="-1" 
          :time-range-weeks="1"
          :auto-refresh="false"
          :use-mock-data="true"
        />
      </div>

      <!-- Test Unknown Account -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Account 99 - Unknown Account (Should show pending)</h2>
        <WeeklySummaryCards 
          :account-id="99" 
          :profile-id="-1" 
          :time-range-weeks="1"
          :auto-refresh="false"
          :use-mock-data="true"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import WeeklySummaryCards from '@/components/WeeklySummaryCards.vue'
</script>
