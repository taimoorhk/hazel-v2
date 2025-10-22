# Final Health Data Integration Guide

## Overview

This guide explains how to integrate the Final Health Data system into your existing dashboards. The system provides comprehensive health analytics from the 3rd checkpoint for each unique user based on their account_id and profile_id.

## Features

- ✅ **User-Specific Data**: Fetches data for any user based on account_id and profile_id
- ✅ **Real-time Sync**: 15-second refresh intervals with real-time data updates
- ✅ **Weekly Report Pending**: Shows "weekly report pending" when data is not available
- ✅ **Comprehensive Health Metrics**: Overall health, cognitive, mental, physical, and social health scores
- ✅ **Risk Assessment**: Alzheimer's, Parkinson's, depression, anxiety, fall, and cognitive risk scores
- ✅ **Health Trends**: Sentiment, engagement, mood stability, and clarity trends
- ✅ **Health Insights**: Strengths, concerns, and recommendations
- ✅ **Data Quality**: Completeness and reliability metrics

## API Endpoints

### Get Final Health Data
```http
POST /api/final-health-data/get-final-data
Content-Type: application/json

{
  "account_id": 6,
  "profile_id": -1
}
```

### Get Multiple Users Data
```http
POST /api/final-health-data/get-multiple-users
Content-Type: application/json

{
  "users": [
    { "account_id": 6, "profile_id": -1 },
    { "account_id": 6, "profile_id": 15 },
    { "account_id": 7, "profile_id": -1 }
  ]
}
```

### Get Real-time Sync Status
```http
GET /api/final-health-data/realtime-sync-status
```

## Component Usage

### Basic Usage
```vue
<template>
  <FinalHealthDataCards
    :account-id="6"
    :profile-id="-1"
    :auto-refresh="true"
    :refresh-interval="15000"
  />
</template>

<script setup>
import FinalHealthDataCards from '@/components/FinalHealthDataCards.vue'
</script>
```

### Using the Composable
```vue
<template>
  <div>
    <div v-if="loading">Loading...</div>
    <div v-else-if="isPending">Weekly report pending</div>
    <div v-else-if="isDataAvailable">
      <h2>Health Score: {{ overallHealthScore }}/10</h2>
      <p>Risk Level: {{ riskLevel }}</p>
    </div>
  </div>
</template>

<script setup>
import { useFinalHealthData } from '@/composables/useFinalHealthData'

const {
  loading,
  isPending,
  isDataAvailable,
  overallHealthScore,
  riskLevel,
  healthData
} = useFinalHealthData({
  accountId: 6,
  profileId: -1,
  autoRefresh: true,
  refreshInterval: 15000
})
</script>
```

## Dashboard Integration Examples

### 1. Main Dashboard Integration
```vue
<template>
  <div class="dashboard">
    <!-- Existing dashboard content -->
    
    <!-- Add Final Health Data Section -->
    <div class="mt-8">
      <h2 class="text-xl font-semibold mb-4">Health Analytics</h2>
      <FinalHealthDataCards
        :account-id="user.account_id"
        :profile-id="user.profile_id"
        :auto-refresh="true"
      />
    </div>
  </div>
</template>
```

### 2. Elderly Profile Dashboard
```vue
<template>
  <div class="elderly-profile-dashboard">
    <div class="profile-header">
      <h1>{{ elderlyProfile.name }}</h1>
      <p>Account: {{ elderlyProfile.account_id }} | Profile: {{ elderlyProfile.profile_id }}</p>
    </div>
    
    <!-- Health Data for this specific elderly profile -->
    <FinalHealthDataCards
      :account-id="elderlyProfile.account_id"
      :profile-id="elderlyProfile.profile_id"
      :auto-refresh="true"
    />
  </div>
</template>
```

### 3. Organization Dashboard (Multiple Users)
```vue
<template>
  <div class="organization-dashboard">
    <h1>Organization Health Overview</h1>
    
    <!-- Multiple users overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="user in organizationUsers"
        :key="`${user.account_id}-${user.profile_id}`"
        class="bg-white rounded-lg shadow p-6"
      >
        <h3 class="font-semibold mb-4">
          {{ user.name }} (Account {{ user.account_id }}, Profile {{ user.profile_id }})
        </h3>
        
        <FinalHealthDataCards
          :account-id="user.account_id"
          :profile-id="user.profile_id"
          :auto-refresh="false"
        />
      </div>
    </div>
  </div>
</template>
```

## Data States

### 1. Data Available
When health data is available, the component shows:
- Overall health score (0-10)
- Individual health scores (cognitive, mental, physical, social)
- Risk assessment scores
- Health trends and patterns
- Health insights and recommendations
- Data quality metrics

### 2. Weekly Report Pending
When no data is available, the component shows:
- "Weekly Report Pending" message
- Explanation that data will be generated once sufficient data is collected
- Last checked timestamp

### 3. Error State
When there's an error fetching data:
- Error message
- Retry functionality
- Fallback to "Weekly Report Pending" state

## Real-time Sync Features

### Auto-refresh
- Default: 15-second intervals
- Configurable refresh intervals
- Automatic retry on failure
- Cache management

### Sync Status
- Real-time sync indicators
- Last updated timestamps
- Sync success/failure status
- Connection health monitoring

## Customization

### Styling
The components use Tailwind CSS classes and can be customized:
```vue
<style scoped>
.final-health-data-cards {
  /* Custom styles */
}
</style>
```

### Configuration
```typescript
const options = {
  accountId: 6,
  profileId: -1,
  autoRefresh: true,
  refreshInterval: 15000, // 15 seconds
  enableCache: true,
  maxRetries: 3
}
```

## Error Handling

### Network Errors
- Automatic retry with exponential backoff
- Fallback to cached data when available
- User-friendly error messages

### Data Errors
- Graceful degradation to "Weekly Report Pending"
- Error logging for debugging
- User notification of issues

## Performance Considerations

### Caching
- 5-minute cache timeout by default
- Automatic cache invalidation
- Memory-efficient cache management

### API Optimization
- Batch requests for multiple users
- Efficient data processing
- Minimal API calls

## Testing

### Unit Tests
```typescript
import { useFinalHealthData } from '@/composables/useFinalHealthData'

test('should fetch health data', async () => {
  const { healthData, fetchHealthData } = useFinalHealthData({
    accountId: 6,
    profileId: -1
  })
  
  await fetchHealthData()
  expect(healthData.value).toBeDefined()
})
```

### Integration Tests
```typescript
test('should show weekly report pending for new user', async () => {
  const response = await finalHealthDataApi.getFinalHealthData(999, 999)
  expect(response.data.has_data).toBe(false)
  expect(response.data.status).toBe('pending')
})
```

## Deployment

### Environment Variables
```env
DIGITALOCEAN_SPACES_KEY=your_key
DIGITALOCEAN_SPACES_SECRET=your_secret
DIGITALOCEAN_SPACES_REGION=your_region
DIGITALOCEAN_SPACES_BUCKET=your_bucket
```

### Database Requirements
- Supabase integration for user management
- Real-time sync capabilities
- Data availability checking

## Monitoring

### Health Checks
- API endpoint availability
- Data freshness monitoring
- Sync status tracking
- Error rate monitoring

### Logging
- Request/response logging
- Error tracking
- Performance metrics
- User activity logs

## Support

For issues or questions:
1. Check the API endpoints are responding
2. Verify user account_id and profile_id are correct
3. Check DigitalOcean Spaces connectivity
4. Review error logs for specific issues

## Future Enhancements

### Planned Features
- Advanced filtering and search
- Export capabilities
- Custom health score thresholds
- Alert system for health changes
- Comparative analysis across users
- Machine learning insights

### API Improvements
- GraphQL support
- WebSocket real-time updates
- Advanced caching strategies
- Rate limiting and throttling
