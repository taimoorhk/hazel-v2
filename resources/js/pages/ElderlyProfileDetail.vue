<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref, onMounted, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { VueUiWheel } from 'vue-data-ui';
import { supabase } from '@/lib/supabaseClient';

// Type definitions
interface ElderlyProfile {
  id: number;
  name: string;
  email: string;
  avatar: string;
  lastActivity: string;
  lastActivityType: 'recent' | 'old';
  registered: string;
  role: string;
  phone?: string;
  status: string;
  created_at: string;
  updated_at: string;
  associated_account_email?: string;
  story?: string;
}

interface CallTranscript {
  id: number;
  call_date: string;
  call_time: string;
  duration_minutes: number;
  transcript_text: string;
  call_type: 'incoming' | 'outgoing';
  status: 'completed' | 'missed' | 'in-progress';
}

interface HealthStats {
  category: string;
  score: number;
  color: string;
}

useAuthGuard();
const { user } = useSupabaseUser();
const page = usePage();

// Reactive data
const profile = ref<ElderlyProfile | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);
const callTranscripts = ref<CallTranscript[]>([]);
const healthStats = ref<HealthStats[]>([]);

// Call minutes data
const totalMinutesUsed = ref(0);
const minutesRemaining = ref(1000); // Default plan
const totalCalls = ref(0);

// Chart configurations
const baseWheelConfig = {
  responsive: false,
  theme: undefined,
  style: {
    fontFamily: 'inherit',
    chart: {
      backgroundColor: 'false',
      color: '#2871B5',
      animation: {
        use: true,
        speed: 0.5,
        acceleration: 1
      },
      hover: {
        enabled: true,
        color: '#2871B5/90'
      },
      layout: {
        wheel: {
          ticks: {
            type: 'classic' as const,
            rounded: true,
            inactiveColor: '#2871B5/20',
            activeColor: '#2871B5',
            sizeRatio: 0.7,
            quantity: 100,
            strokeWidth: 5,
            gradient: {
              show: false
            }
          }
        },
        innerCircle: {
          show: false,
          stroke: '#2871B5',
          strokeWidth: 1
        },
        percentage: {
          show: true,
          fontSize: 36,
          rounding: 0,
          bold: true,
          formatter: null,
          color: '#2871B5'
        }
      }
    }
  },
  userOptions: {
    show: false,
    showOnChartHover: false,
    keepStateOnChartLeave: false,
    position: 'right' as const,
    buttons: {
      tooltip: false,
      pdf: false,
      csv: false,
      img: false,
      table: false,
      labels: false,
      fullscreen: false,
      sort: false,
      stack: false,
      animation: false,
      annotator: false
    },
    callbacks: {
      animation: null,
      annotator: null,
      csv: null,
      fullscreen: null,
      img: null,
      labels: null,
      pdf: null,
      sort: null,
      stack: null,
      table: null,
      tooltip: null
    }
  }
};

onMounted(async () => {
  const profileId = page.props.id;
  if (profileId) {
    await fetchProfileDetails(profileId as string);
    await fetchCallTranscripts(profileId as string);
    await fetchHealthStats(profileId as string);
    await fetchCallMinutes(profileId as string);
  }
});

const fetchProfileDetails = async (profileId: string) => {
  try {
    loading.value = true;
    const { data, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('id', profileId)
      .single();

    if (fetchError) throw fetchError;
    
    // Generate avatar URL like in ElderlyProfiles.vue
    if (data) {
      data.avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name || data.email.split('@')[0])}&background=random&size=128`;
    }
    
    profile.value = data;
  } catch (err: any) {
    error.value = err.message || 'Failed to fetch profile details';
  } finally {
    loading.value = false;
  }
};

const fetchCallTranscripts = async (profileId: string) => {
  try {
    // Mock data for now - replace with actual API call
    callTranscripts.value = [
      {
        id: 1,
        call_date: '2024-01-15',
        call_time: '14:30',
        duration_minutes: 25,
        transcript_text: 'Hello, how are you feeling today?',
        call_type: 'outgoing',
        status: 'completed'
      },
      {
        id: 2,
        call_date: '2024-01-14',
        call_time: '10:15',
        duration_minutes: 18,
        transcript_text: 'Good morning! How did you sleep?',
        call_type: 'incoming',
        status: 'completed'
      },
      {
        id: 3,
        call_date: '2024-01-13',
        call_time: '16:45',
        duration_minutes: 32,
        transcript_text: 'Let\'s check your medication schedule.',
        call_type: 'outgoing',
        status: 'completed'
      }
    ];
  } catch (err: any) {
    console.error('Error fetching call transcripts:', err);
  }
};

const fetchHealthStats = async (profileId: string) => {
  try {
    // Mock data for now - replace with actual API call
    healthStats.value = [
      { category: 'Mobility', score: 85, color: '#2871B5' },
      { category: 'Memory', score: 92, color: '#2871B5' },
      { category: 'Communication', score: 78, color: '#2871B5' },
      { category: 'Daily Activities', score: 88, color: '#2871B5' }
    ];
  } catch (err: any) {
    console.error('Error fetching health stats:', err);
  }
};

const fetchCallMinutes = async (profileId: string) => {
  try {
    // Calculate total minutes from transcripts
    totalMinutesUsed.value = callTranscripts.value.reduce((total, call) => total + call.duration_minutes, 0);
    totalCalls.value = callTranscripts.value.length;
    minutesRemaining.value = Math.max(0, 1000 - totalMinutesUsed.value);
  } catch (err: any) {
    console.error('Error calculating call minutes:', err);
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatTime = (timeString: string) => {
  return timeString;
};

const getCallTypeIcon = (type: string) => {
  return type === 'incoming' ? '📥' : '📤';
};

const getCallStatusColor = (status: string) => {
  switch (status) {
    case 'completed': return 'text-green-600';
    case 'missed': return 'text-red-600';
    case 'in-progress': return 'text-yellow-600';
    default: return 'text-gray-600';
  }
};

const goBack = () => {
  window.history.back();
};
</script>

<template>
  <AppLayout>
    <Head :title="profile ? `${profile.name} - Profile` : 'Elderly Profile'" />
    
    <div v-if="loading" class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
        <p class="text-[#2871B5]/70">Loading profile details...</p>
      </div>
    </div>

    <div v-else-if="error" class="flex items-center justify-center h-96">
      <div class="text-center">
        <p class="text-red-600 mb-4">{{ error }}</p>
        <button @click="goBack" class="px-4 py-2 bg-[#2871B5] text-white rounded-md hover:bg-[#2871B5]/80">
          Go Back
        </button>
      </div>
    </div>

    <div v-else-if="profile" class="p-8">
      <!-- Header -->
      <div class="flex items-center gap-4 mb-8">
        <button @click="goBack" class="p-2 text-[#2871B5] hover:bg-[#2871B5]/10 rounded-lg transition-colors">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <div class="flex items-center gap-4">
          <img :src="profile.avatar" alt="avatar" class="w-16 h-16 rounded-full object-cover border-2 border-[#2871B5]/20" />
          <div>
            <h1 class="text-3xl font-bold text-black">{{ profile.name }}</h1>
            <p class="text-black/60">{{ profile.email }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-medium',
                profile.status === 'active' 
                  ? 'bg-[#2871B5]/10 text-[#2871B5]' 
                  : 'bg-red-100 text-red-800'
              ]">
                {{ profile.status === 'active' ? 'Active' : 'Deactivated' }}
              </span>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#2871B5]/10 text-[#2871B5]">
                {{ profile.role }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Call Minutes Overview -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-[#2871B5]/10 rounded-lg flex items-center justify-center">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" class="text-[#2871B5]">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-black/70">Minutes Used</h3>
              <p class="text-2xl font-bold text-black">{{ totalMinutesUsed }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-[#2871B5]/10 rounded-lg flex items-center justify-center">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" class="text-[#2871B5]">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-black/70">Minutes Remaining</h3>
              <p class="text-2xl font-bold text-black">{{ minutesRemaining }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-[#2871B5]/10 rounded-lg flex items-center justify-center">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" class="text-[#2871B5]">
                <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-medium text-black/70">Total Calls</h3>
              <p class="text-2xl font-bold text-black">{{ totalCalls }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Health Stats Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-[#2871B5] mb-4">Health Assessment Overview</h3>
          <div class="grid grid-cols-2 gap-4">
            <div v-for="stat in healthStats" :key="stat.category" class="text-center">
              <div class="mb-2">
                <VueUiWheel :dataset="{ percentage: stat.score }" :config="baseWheelConfig" />
              </div>
              <p class="text-sm font-medium text-[#2871B5]">{{ stat.category }}</p>
              <p class="text-xs text-[#2871B5]/60">{{ stat.score }}%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm">
          <h3 class="text-lg font-semibold text-[#2871B5] mb-4">Profile Information</h3>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-sm text-black/60">Phone:</span>
              <span class="text-sm text-black">{{ profile.phone || 'Not provided' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-black/60">Registered:</span>
              <span class="text-sm text-black">{{ formatDate(profile.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm text-black/60">Last Activity:</span>
              <span class="text-sm text-black">{{ formatDate(profile.updated_at) }}</span>
            </div>
            <div v-if="profile.story" class="mt-4">
              <span class="text-sm text-black/60 block mb-2">Story:</span>
              <p class="text-sm text-black bg-[#2871B5]/5 p-3 rounded-lg">{{ profile.story }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Call Transcripts Table -->
      <div class="bg-white rounded-xl border border-neutral-200 shadow-sm">
        <div class="p-6 border-b border-neutral-200">
          <h3 class="text-lg font-semibold text-[#2871B5]">Call History & Transcripts</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-[#2871B5]/5">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-[#2871B5] uppercase tracking-wider">Date & Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-[#2871B5] uppercase tracking-wider">Duration</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-[#2871B5] uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-[#2871B5] uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-[#2871B5] uppercase tracking-wider">Transcript</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-neutral-200">
              <tr v-for="transcript in callTranscripts" :key="transcript.id" class="hover:bg-[#2871B5]/5">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-black">{{ formatDate(transcript.call_date) }}</div>
                  <div class="text-sm text-black/60">{{ formatTime(transcript.call_time) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                  {{ transcript.duration_minutes }} min
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="text-2xl">{{ getCallTypeIcon(transcript.call_type) }}</span>
                  <span class="text-sm text-black ml-2 capitalize">{{ transcript.call_type }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="['px-2 py-1 text-xs font-medium rounded-full capitalize', getCallStatusColor(transcript.status)]">
                    {{ transcript.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-black max-w-xs truncate" :title="transcript.transcript_text">
                    {{ transcript.transcript_text }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
