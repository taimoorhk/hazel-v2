<template>
  <section class="weekly-summary-section">
    <h3 class="section-title">Weekly Summary</h3>
    

    <div class="summary-grid">
      <!-- Total Conversations Card -->
      <div class="summary-card">
        <div class="summary-icon">💬</div>
        <div class="summary-content">
          <div class="summary-label">Total Conversations</div>
          <div class="summary-value" v-if="hasConversationData">{{ conversationData?.total_conversations || 0 }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>

      <!-- Total Duration Card -->
      <div class="summary-card">
        <div class="summary-icon">⏱️</div>
        <div class="summary-content">
          <div class="summary-label">Total Duration</div>
          <div class="summary-value" v-if="hasConversationData">{{ formatDuration(conversationData?.total_duration || 0) }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>

      <!-- Average Sentiment Card -->
      <div class="summary-card">
        <div class="summary-icon">😊</div>
        <div class="summary-content">
          <div class="summary-label">Average Sentiment</div>
          <div class="summary-value" v-if="hasConversationData">{{ conversationData?.average_sentiment || 'Neutral' }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>

      <!-- Key Topics Card -->
      <div class="summary-card">
        <div class="summary-icon">🏷️</div>
        <div class="summary-content">
          <div class="summary-label">Key Topics</div>
          <div class="summary-value" v-if="hasSummaryData">{{ formatTopics(summaryData?.key_topics || []) }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>

      <!-- Health Insights Card -->
      <div class="summary-card">
        <div class="summary-icon">💡</div>
        <div class="summary-content">
          <div class="summary-label">Health Insights</div>
          <div class="summary-value" v-if="hasSummaryData">{{ formatInsights(summaryData?.health_insights || []) }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>

      <!-- Weekly Trends Card -->
      <div class="summary-card">
        <div class="summary-icon">📈</div>
        <div class="summary-content">
          <div class="summary-label">Weekly Trends</div>
          <div class="summary-value" v-if="hasSummaryData">{{ formatTrends(summaryData?.weekly_trends || []) }}</div>
          <div class="summary-value pending" v-else>Weekly stats pending</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { weeklySummaryApi } from '@/lib/weeklySummaryApi';

interface Props {
  accountId: number;
  profileId: number;
  timeRangeWeeks?: number;
}

const props = withDefaults(defineProps<Props>(), {
  timeRangeWeeks: 1
});

const loading = ref(false);
const error = ref<string | null>(null);
const healthData = ref<any>(null);
const conversationData = ref<any>(null);
const summaryData = ref<any>(null);

const hasConversationData = computed(() => {
  return conversationData.value && conversationData.value.success;
});

const hasSummaryData = computed(() => {
  return summaryData.value && summaryData.value.success;
});


const loadAllData = async () => {
  loading.value = true;
  error.value = null;

  try {
    const allData = await weeklySummaryApi.getAllData(
      props.accountId,
      props.profileId,
      props.timeRangeWeeks
    );

    healthData.value = allData.health;
    conversationData.value = allData.conversations;
    summaryData.value = allData.summary;

    console.log('📊 All API Data:', allData);
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unknown error occurred';
    console.error('Error loading all data:', err);
  } finally {
    loading.value = false;
  }
};

const formatDuration = (seconds: number): string => {
  if (seconds < 60) return `${seconds}s`;
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  if (minutes < 60) return `${minutes}m ${remainingSeconds}s`;
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return `${hours}h ${remainingMinutes}m`;
};

const formatTopics = (topics: string[]): string => {
  if (!topics || topics.length === 0) return 'None';
  return topics.slice(0, 3).join(', ') + (topics.length > 3 ? '...' : '');
};

const formatInsights = (insights: string[]): string => {
  if (!insights || insights.length === 0) return 'No insights';
  return insights[0] || 'No insights';
};

const formatTrends = (trends: string[]): string => {
  if (!trends || trends.length === 0) return 'No trends';
  return trends[0] || 'No trends';
};

onMounted(() => {
  loadAllData();
});
</script>

<style scoped>
.weekly-summary-section {
  margin-bottom: 40px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 16px;
  padding-bottom: 8px;
  border-bottom: 2px solid #e5e7eb;
}


.summary-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 12px;
}

.summary-card {
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

.summary-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.summary-icon {
  font-size: 24px;
  margin-bottom: 4px;
}

.summary-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.summary-label {
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
  text-align: center;
  line-height: 1.2;
}

.summary-value {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
  text-align: center;
  line-height: 1.2;
}

.summary-value.pending {
  color: #9ca3af;
  font-style: italic;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .summary-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  
  .summary-card {
    padding: 12px;
  }
  
  .summary-label {
    font-size: 10px;
  }
  
  .summary-value {
    font-size: 14px;
  }
}

@media (max-width: 768px) {
  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  
  .summary-card {
    padding: 10px;
  }
  
  .summary-icon {
    font-size: 20px;
  }
  
  .summary-label {
    font-size: 9px;
  }
  
  .summary-value {
    font-size: 12px;
  }
}

@media (max-width: 480px) {
  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }
  
  .summary-card {
    padding: 8px;
  }
  
  .summary-icon {
    font-size: 18px;
  }
  
  .summary-label {
    font-size: 8px;
  }
  
  .summary-value {
    font-size: 11px;
  }
}
</style>
