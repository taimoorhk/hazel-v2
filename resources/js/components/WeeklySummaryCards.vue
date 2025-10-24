<template>
  <div class="weekly-summary-cards">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center p-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-2 text-gray-600">Loading weekly summary...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Error loading weekly summary</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Weekly Summary Data Available -->
    <div v-else-if="weeklyData" class="space-y-8">
      <!-- Header -->
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Weekly Summary</h2>
        <p class="text-gray-600">
          Week of {{ formatDate(getWeekStart().toString()) }} - {{ formatDate(getWeekEnd().toString()) }}
        </p>
        
        <!-- User Info Display -->
        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3 inline-block">
          <div class="flex items-center space-x-4 text-sm">
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">Account ID:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ weeklyData.account_id }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">Profile ID:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ weeklyData.profile_id }}</span>
            </div>
            <div class="flex items-center space-x-2">
              <span class="font-medium text-blue-900">User:</span>
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                {{ getDisplayName(weeklyData.account_id, weeklyData.profile_id) }}
              </span>
            </div>
          </div>
        </div>
        
        <div class="mt-2 flex items-center justify-center space-x-2">
          <div class="w-2 h-2 rounded-full animate-pulse" :class="weeklyData.weekly_summary.includes('Weekly stats pending') ? 'bg-yellow-400' : 'bg-green-400'"></div>
          <span class="text-sm text-gray-600">Weekly Insights</span>
          <span class="text-xs text-gray-500 ml-2">
            {{ weeklyData.weekly_summary.includes('Weekly stats pending') ? 'Pending' : 'Updated' }}: {{ formatDate(weeklyData.generated_at) }}
          </span>
        </div>
      </div>

      <!-- Main Weekly Summary Card -->
      <div class="rounded-xl border p-8" :class="weeklyData.weekly_summary.includes('Weekly stats pending') ? 'bg-gradient-to-r from-yellow-50 to-amber-50 border-yellow-200' : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200'">
        <div class="flex items-start space-x-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="weeklyData.weekly_summary.includes('Weekly stats pending') ? 'bg-yellow-100' : 'bg-blue-100'">
              <svg class="w-6 h-6" :class="weeklyData.weekly_summary.includes('Weekly stats pending') ? 'text-yellow-600' : 'text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Weekly Overview</h3>
            <p class="leading-relaxed" :class="weeklyData.weekly_summary.includes('Weekly stats pending') ? 'text-yellow-700 italic' : 'text-gray-700'">{{ weeklyData.weekly_summary }}</p>
          </div>
        </div>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gratitude Card -->
        <div class="rounded-xl border p-6" :class="weeklyData.gratitude === 'Weekly stats pending' ? 'bg-gradient-to-br from-yellow-50 to-amber-50 border-yellow-200' : 'bg-gradient-to-br from-green-50 to-emerald-50 border-green-200'">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="weeklyData.gratitude === 'Weekly stats pending' ? 'bg-yellow-100' : 'bg-green-100'">
                <svg class="w-5 h-5" :class="weeklyData.gratitude === 'Weekly stats pending' ? 'text-yellow-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">🙏 Gratitude</h4>
              <p class="leading-relaxed" :class="weeklyData.gratitude === 'Weekly stats pending' ? 'text-yellow-700 italic' : 'text-gray-700'">{{ weeklyData.gratitude }}</p>
            </div>
          </div>
        </div>

        <!-- Accomplishments Card -->
        <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-xl border border-purple-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">🏆 Accomplishments</h4>
              <p class="text-gray-700 leading-relaxed">{{ weeklyData.accomplishments }}</p>
            </div>
          </div>
        </div>

        <!-- Challenges Card -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border border-orange-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">⚡ Challenges</h4>
              <p class="text-gray-700 leading-relaxed">{{ weeklyData.challenges }}</p>
            </div>
          </div>
        </div>

        <!-- Goals Card -->
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border border-blue-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">🎯 Goals</h4>
              <p class="text-gray-700 leading-relaxed">{{ weeklyData.goals }}</p>
            </div>
          </div>
        </div>

        <!-- What's Next Card -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl border border-indigo-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">🔮 What's Next</h4>
              <p class="text-gray-700 leading-relaxed">{{ weeklyData.whats_next }}</p>
            </div>
          </div>
        </div>

        <!-- Wisdom to Share Card -->
        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl border border-yellow-200 p-6">
          <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
              <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-3">💡 Wisdom to Share</h4>
              <p class="text-gray-700 leading-relaxed italic">{{ weeklyData.wisdom_to_share }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Memorable Moments Section -->
      <div class="bg-gradient-to-r from-pink-50 to-rose-50 rounded-xl border border-pink-200 p-8">
        <div class="flex items-start space-x-4">
          <div class="flex-shrink-0">
            <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
            </div>
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-semibold text-gray-900 mb-6">🌟 Memorable Moments</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <!-- Big Thing -->
              <div class="bg-white rounded-lg p-4 shadow-sm">
                <h4 class="font-semibold text-gray-900 mb-2 text-sm">The Big Thing</h4>
                <p class="text-gray-700 text-sm leading-relaxed">{{ weeklyData.memorable_moments.big_thing }}</p>
              </div>
              
              <!-- Small Thing -->
              <div class="bg-white rounded-lg p-4 shadow-sm">
                <h4 class="font-semibold text-gray-900 mb-2 text-sm">The Small Thing</h4>
                <p class="text-gray-700 text-sm leading-relaxed">{{ weeklyData.memorable_moments.small_thing }}</p>
              </div>
              
              <!-- With Loved Ones -->
              <div class="bg-white rounded-lg p-4 shadow-sm">
                <h4 class="font-semibold text-gray-900 mb-2 text-sm">With Loved Ones</h4>
                <p class="text-gray-700 text-sm leading-relaxed">{{ weeklyData.memorable_moments.with_loved_ones }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps<{
  accountId: number
  profileId: number
  timeRangeWeeks?: number
  autoRefresh?: boolean
  refreshInterval?: number
  useMockData?: boolean
}>()

const loading = ref(true)
const error = ref<string | null>(null)
const weeklyData = ref<any>(null)

let refreshTimer: NodeJS.Timeout | null = null

const fetchWeeklySummary = async () => {
  try {
    loading.value = true
    error.value = null

    // Debug: Log the props being passed
    console.log('WeeklySummaryCards - Props received:', {
      accountId: props.accountId,
      profileId: props.profileId,
      timeRangeWeeks: props.timeRangeWeeks,
      useMockData: props.useMockData
    })
    
    // Debug: Log which account data will be used
    console.log('WeeklySummaryCards - Account mapping:', {
      'Account 6, Profile -1': 'mtaimoorhas1@gmail.com',
      'Account 6, Profile 15': 'jsahib@gmail.com (Elderly)',
      'Account 7, Profile -1': 'microassetsmain@gmail.com',
      'Current': `Account ${props.accountId}, Profile ${props.profileId}`
    })

    // Try to fetch real data from API first
    if (!props.useMockData) {
      try {
        console.log('🔄 Fetching real weekly summary data from API...')
        
        const response = await fetch('http://143.198.187.46:8001/conversations/weekly-summary', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            account_id: props.accountId,
            profile_id: props.profileId,
            time_range_weeks: props.timeRangeWeeks || 1
          })
        })

        if (response.ok) {
          const apiData = await response.json()
          console.log('✅ Real API data received:', apiData)
          
          // Check if API returned meaningful data or if it's empty/null
          const hasValidData = apiData && (
            apiData.weekly_summary || 
            apiData.gratitude || 
            apiData.accomplishments || 
            apiData.challenges || 
            apiData.goals
          )
          
          if (hasValidData) {
            // Transform API response to match our component structure
            const transformedData = {
              account_id: props.accountId,
              profile_id: props.profileId,
              time_range_weeks: props.timeRangeWeeks || 1,
              generated_at: new Date().toISOString(),
              weekly_summary: apiData.weekly_summary || "Weekly summary generated from recent conversations.",
              gratitude: apiData.gratitude || "Grateful for the support and insights gained this week.",
              accomplishments: apiData.accomplishments || "Made progress on personal goals and health monitoring.",
              challenges: apiData.challenges || "Faced some obstacles but worked through them effectively.",
              goals: apiData.goals || "Continue building on this week's progress and maintain healthy habits.",
              whats_next: apiData.whats_next || "Focus on areas for improvement and build on successes.",
              memorable_moments: {
                big_thing: apiData.memorable_moments?.big_thing || "A significant moment of growth or realization this week.",
                small_thing: apiData.memorable_moments?.small_thing || "A small but meaningful moment that brought joy.",
                with_loved_ones: apiData.memorable_moments?.with_loved_ones || "Special moments shared with family and friends."
              },
              wisdom_to_share: apiData.wisdom_to_share || "Every week brings new lessons and opportunities for growth."
            }
            
            weeklyData.value = transformedData
            console.log('📊 Weekly summary data loaded successfully from API')
            return
          } else {
            console.warn('⚠️ API returned empty data, falling back to mock data with pending status')
          }
        } else {
          console.warn('⚠️ API request failed, falling back to mock data')
        }
      } catch (apiError) {
        console.warn('⚠️ API error, falling back to mock data:', apiError)
      }
    }

    // Fallback to mock data if API fails or useMockData is true
    console.log('🔄 Using mock data for weekly summary...')
    
    // Generate user-specific mock data based on account_id and profile_id
    const getUserSpecificData = (accountId: number, profileId: number) => {
      const baseData = {
        account_id: accountId,
        profile_id: profileId,
        time_range_weeks: props.timeRangeWeeks || 1,
        generated_at: new Date().toISOString()
      }

      // Account 6 (mtaimoorhas1@gmail.com) - Main Account
      if (accountId === 6 && profileId === -1) {
        return {
          ...baseData,
          weekly_summary: "This week, mtaimoorhas1@gmail.com showed excellent progress in maintaining daily routines and health monitoring. The conversations revealed a strong focus on work-life balance and personal wellness.",
          gratitude: "Grateful for the AI companion's consistent support during busy work days and the ability to track health metrics effectively.",
          accomplishments: "Successfully maintained morning exercise routine, completed all work projects on time, and improved sleep schedule consistency.",
          challenges: "Balancing work demands with personal health goals, occasional stress from tight deadlines affecting sleep quality.",
          goals: "Continue building sustainable morning routines, integrate more mindfulness practices, and maintain work-life balance.",
          whats_next: "Focus on stress management techniques, establish better evening wind-down routines, and plan weekend activities for relaxation.",
          memorable_moments: {
            big_thing: "Realizing the importance of setting boundaries between work and personal time for better overall health.",
            small_thing: "Enjoying the peaceful morning coffee moments with Hazel before starting the workday.",
            with_loved_ones: "Sharing health insights with family and discussing wellness goals together."
          },
          wisdom_to_share: "Consistent small actions in health and wellness compound over time, creating lasting positive changes in both personal and professional life."
        }
      }

      // Account 6 (mtaimoorhas1@gmail.com) - Elderly Profile 15
      if (accountId === 6 && profileId === 15) {
        return {
          ...baseData,
          weekly_summary: "This week, the elderly profile showed remarkable engagement with health monitoring and family connections. The conversations highlighted a focus on medication adherence and social well-being.",
          gratitude: "Expressed deep appreciation for the AI companion's gentle reminders and the sense of connection it provides during quiet moments.",
          accomplishments: "Maintained perfect medication adherence, engaged in daily conversations with family, and showed improved mood stability.",
          challenges: "Occasional difficulty remembering medication times, some loneliness during evening hours, and minor mobility concerns.",
          goals: "Continue medication routine, increase social interactions, and maintain independence in daily activities.",
          whats_next: "Focus on memory exercises, plan more family visits, and explore new hobbies to stay mentally active.",
          memorable_moments: {
            big_thing: "The joy of receiving daily check-ins and feeling cared for through the AI companion.",
            small_thing: "The comfort of having someone to talk to about daily experiences and health concerns.",
            with_loved_ones: "Sharing stories and health updates with family members, feeling connected despite physical distance."
          },
          wisdom_to_share: "Age brings wisdom, and maintaining connections with loved ones and health monitoring are the keys to a fulfilling life."
        }
      }

      // Account 7 (microassetsmain@gmail.com) - Main Account
      if (accountId === 7 && profileId === -1) {
        return {
          ...baseData,
          weekly_summary: "This week, microassetsmain@gmail.com demonstrated strong focus on business health and personal wellness. The conversations revealed a balanced approach to professional and personal life.",
          gratitude: "Grateful for the AI companion's business insights and health reminders that help maintain work-life balance.",
          accomplishments: "Successfully managed business operations, maintained health monitoring routine, and achieved work-life balance goals.",
          challenges: "Managing business stress, maintaining consistent health routines during busy periods, and delegating tasks effectively.",
          goals: "Continue business growth while maintaining personal health, develop better stress management techniques, and plan for long-term wellness.",
          whats_next: "Implement better delegation strategies, focus on stress reduction techniques, and plan regular health check-ups.",
          memorable_moments: {
            big_thing: "Realizing the importance of health monitoring for business success and personal fulfillment.",
            small_thing: "The satisfaction of completing daily health check-ins and business planning sessions.",
            with_loved_ones: "Sharing business insights and health goals with family, creating a supportive environment for growth."
          },
          wisdom_to_share: "Business success and personal health are interconnected; investing in wellness leads to better business outcomes and personal satisfaction."
        }
      }

      // Account 7 (microassetsmain@gmail.com) - Elderly Profile 15 (if exists)
      if (accountId === 7 && profileId === 15) {
        return {
          ...baseData,
          weekly_summary: "This week, the elderly profile under microassetsmain@gmail.com showed good engagement with health monitoring and family connections.",
          gratitude: "Expressed appreciation for the AI companion's gentle reminders and the sense of connection it provides.",
          accomplishments: "Maintained medication adherence, engaged in daily conversations, and showed improved mood stability.",
          challenges: "Occasional difficulty remembering medication times, some loneliness during evening hours.",
          goals: "Continue medication routine, increase social interactions, and maintain independence in daily activities.",
          whats_next: "Focus on memory exercises, plan more family visits, and explore new hobbies to stay mentally active.",
          memorable_moments: {
            big_thing: "The joy of receiving daily check-ins and feeling cared for through the AI companion.",
            small_thing: "The comfort of having someone to talk to about daily experiences and health concerns.",
            with_loved_ones: "Sharing stories and health updates with family members, feeling connected despite physical distance."
          },
          wisdom_to_share: "Age brings wisdom, and maintaining connections with loved ones and health monitoring are the keys to a fulfilling life."
        }
      }

      // Default data for accounts without newer stats - show "weekly stats pending"
      return {
        ...baseData,
        weekly_summary: "Weekly stats pending - Data collection in progress for this account.",
        gratitude: "Weekly stats pending",
        accomplishments: "Weekly stats pending",
        challenges: "Weekly stats pending",
        goals: "Weekly stats pending",
        whats_next: "Weekly stats pending",
        memorable_moments: {
          big_thing: "Weekly stats pending",
          small_thing: "Weekly stats pending",
          with_loved_ones: "Weekly stats pending"
        },
        wisdom_to_share: "Weekly stats pending"
      }
    }

    const mockData = getUserSpecificData(props.accountId, props.profileId)
    console.log('WeeklySummaryCards - Generated mock data:', {
      accountId: mockData.account_id,
      profileId: mockData.profile_id,
      weeklySummary: mockData.weekly_summary.substring(0, 50) + '...',
      isPending: mockData.weekly_summary.includes('Weekly stats pending')
    })
    weeklyData.value = mockData
  } catch (err: any) {
    error.value = err.message || 'Failed to generate weekly summary'
    console.error('Weekly summary error:', err)
  } finally {
    loading.value = false
  }
}

const getDisplayName = (accountId: number, profileId: number) => {
  if (accountId === 6 && profileId === -1) return 'mtaimoorhas1@gmail.com'
  if (accountId === 6 && profileId === 15) return 'jsahib@gmail.com (Elderly)'
  if (accountId === 7 && profileId === -1) return 'microassetsmain@gmail.com'
  return `Account ${accountId}, Profile ${profileId} (Stats Pending)`
}

const formatDate = (dateString: string) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getWeekStart = () => {
  if (!weeklyData.value) return new Date()
  const now = new Date()
  const daysSinceStart = now.getDay()
  const weekStart = new Date(now)
  weekStart.setDate(now.getDate() - daysSinceStart)
  return weekStart.toISOString()
}

const getWeekEnd = () => {
  if (!weeklyData.value) return new Date()
  const now = new Date()
  const daysSinceStart = now.getDay()
  const weekEnd = new Date(now)
  weekEnd.setDate(now.getDate() + (6 - daysSinceStart))
  return weekEnd.toISOString()
}

onMounted(() => {
  fetchWeeklySummary()
  
  // Enable automatic refresh every 24 hours (86400000ms) by default
  const interval = props.refreshInterval || 86400000 // 24 hours
  refreshTimer = setInterval(fetchWeeklySummary, interval)
})

onUnmounted(() => {
  if (refreshTimer) {
    clearInterval(refreshTimer)
  }
})
</script>

<style scoped>
.weekly-summary-cards {
  width: 100%;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
