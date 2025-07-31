<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardAction } from '@/components/ui/card';
import Icon from '@/components/Icon.vue';
import { VueUiWheel } from 'vue-data-ui';
import { VueUiTiremarks } from 'vue-data-ui';
import { ref, onMounted, onUnmounted } from 'vue';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { Link } from '@inertiajs/vue3';
import { supabase } from '@/lib/supabaseClient';
import type { User } from '@supabase/supabase-js';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { usePostHog } from '@/composables/usePostHog';

const { loading } = useAuthGuard();
const { user, session } = useSupabaseUser();
const { posthog } = usePostHog();

// Add a ref for the latest user details
const realtimeUser = ref<User | null>(null);
const showRoleChangeDialog = ref(false);
const roleChangeLoading = ref(false);
const roleChangeError = ref('');


onMounted(async () => {
  // Track dashboard page view
  posthog.capture('dashboard_viewed', {
    user_email: user.value?.email,
    user_id: user.value?.id,
  });
  
  // Wait for session to be available
  const waitForSession = async () => {
    if (session) {
      // Fetch the latest user details from Supabase
      const { data, error } = await supabase.auth.getUser();
      if (data && data.user) {
        realtimeUser.value = data.user;
      }
      // Listen for user changes in realtime
      supabase.auth.onAuthStateChange((_event, newSession) => {
        if (newSession && newSession.user) {
          realtimeUser.value = newSession.user;
        }
      });
      
      // Check user questions from backend with a small delay to ensure user is loaded
      setTimeout(async () => {
        await checkUserQuestionsFromBackend();
      }, 1000);
      
      // Start continuous polling every 2 seconds for real-time updates
      continuousPollingInterval = window.setInterval(async () => {
        const hasQuestions = await checkUserQuestionsFromBackend();
        // Force reactivity update
        if (hasQuestions) {
          // User questions detected, UI will update automatically
        }
      }, 2000);
    } else {
      setTimeout(waitForSession, 500);
    }
  };
  
  await waitForSession();
});

function getRandomScore() {
  return Math.floor(Math.random() * 81) + 10; // 10-90 for visible movement
}
function getRandomChange() {
  return parseFloat((Math.random() * 20 - 10).toFixed(1));
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const stats = [
    {
        title: 'Elderly Profiles',
        value: 14,
        change: -10.6,
        icon: 'users',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
    },
    {
        title: 'Total Calls',
        value: 22,
        change: 27.5,
        icon: 'phone',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
    },
    {
        title: 'Minutes Used',
        value: 16,
        change: -1.6,
        icon: 'clock',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
    },
    {
        title: 'Credits Remaining',
        value: 20,
        change: 12.3,
        icon: 'wallet',
        iconBg: 'bg-green-100',
        iconColor: 'text-green-600',
    },
];

const elderlyProfiles = [
  { name: 'Alice Johnson', score: 85, change: 2.5 },
  { name: 'Bob Smith', score: 72, change: -1.2 },
  { name: 'Clara Lee', score: 90, change: 3.1 },
  { name: 'David Kim', score: 65, change: -0.8 },
  { name: 'Emma Brown', score: 78, change: 1.7 },
  { name: 'Frank Green', score: 88, change: 2.0 },
];

const baseWheelConfig = {
    responsive: false,
    theme: undefined,
    style: {
        fontFamily: 'inherit',
        chart: {
            backgroundColor: 'false',
            color: '#1A1A1Aff',
            animation: {
                use: true,
                speed: 0.5,
                acceleration: 1
            },
            layout: {
                wheel: {
                    ticks: {
                        type: 'classic' as const,
                        rounded: true,
                        inactiveColor: '#e1e5e8',
                        activeColor: '#00a53d',
                        sizeRatio: 0.7,
                        quantity: 100,
                        strokeWidth: 5,
                        gradient: {
                            show: true,
                            shiftHueIntensity: 10
                        }
                    }
                },
                innerCircle: {
                    show: false,
                    stroke: '#e1e5e8ff',
                    strokeWidth: 1
                },
                percentage: {
                    show: true,
                    fontSize: 48,
                    rounding: 0,
                    bold: true,
                    formatter: null
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
        },
        buttonTitles: {
            open: 'Open options',
            close: 'Close options',
            pdf: 'Download PDF',
            img: 'Download PNG',
            fullscreen: 'Toggle fullscreen',
            annotator: 'Toggle annotator'
        },
        print: {
            allowTaint: false,
            backgroundColor: '#FFFFFFff',
            useCORS: false,
            onclone: null,
            scale: 2,
            logging: false
        }
    }
};

const tiremarksConfig = ref({
    theme: undefined,
    style: {
        fontFamily: 'inherit',
        chart: {
            backgroundColor: 'false',
            color: '#1A1A1Aff',
            animation: {
                use: true,
                speed: 0.5,
                acceleration: 1
            },
            layout: {
                display: 'horizontal' as 'horizontal',
                crescendo: false,
                curved: false,
                curveAngleX: 10,
                curveAngleY: 10,
                activeColor: '#00a53d',
                inactiveColor: '#e1e5e8',
                ticks: {
                    gradient: {
                        show: true,
                        shiftHueIntensity: 10
                    }
                }
            },
            percentage: {
                show: true,
                useGradientColor: true,
                color: '#1A1A1Aff',
                fontSize: 16,
                bold: true,
                rounding: 0,
                verticalPosition: 'bottom' as 'bottom',
                horizontalPosition: 'left' as 'left',
                formatter: null
            },
            
        }
    },
    userOptions: {
        show: false,
        showOnChartHover: false,
        keepStateOnChartLeave: false,
        position: 'right' as 'right',
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
        },
        buttonTitles: {
            open: 'Open options',
            close: 'Close options',
            pdf: 'Download PDF',
            img: 'Download PNG',
            fullscreen: 'Toggle fullscreen',
            annotator: 'Toggle annotator'
        },
        print: {
            allowTaint: false,
            backgroundColor: '#FFFFFFff',
            useCORS: false,
            onclone: null,
            scale: 2,
            logging: false
        }
    }
});

const userQuestions = ref<string | null>(null);
const userQuestionsLoaded = ref(false);
const onboardingLoading = ref(false);
const userRole = ref<string | null>(null);
const onboardingFormLink = 'https://example.com/onboarding-form'; // Example form link

let pollingInterval: number | null = null;
let continuousPollingInterval: number | null = null;

async function openOnboardingForm() {
  window.open(onboardingFormLink, '_blank');
  // Start polling for user_questions update from backend
  pollingInterval = window.setInterval(async () => {
    const { data } = await supabase.auth.getUser();
    if (data && data.user) {
      // Check backend for user_questions
      const response = await fetch('/api/check-user-questions', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email: data.user.email
        })
      });
      
      if (response.ok) {
        const result = await response.json();
        if (result.has_questions) {
          if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
          }
          // Refresh user data and update local state
          realtimeUser.value = data.user;
          userQuestions.value = result.user_questions;
          userRole.value = result.role;
        }
      }
    }
  }, 2000); // Poll every 2 seconds
}

function openOnboardingFormLink() {
  // Track onboarding form link click
  posthog.capture('onboarding_form_clicked', {
    user_email: user.value?.email,
    user_id: user.value?.id,
    form_url: 'https://form.fillout.com/t/hZP4BtRFr4us',
  });
  
  window.open('https://form.fillout.com/t/hZP4BtRFr4us', '_blank');
}

function isNormalUser() {
  // First check the role from the API response
  if (userRole.value) {
    return userRole.value === 'Normal User';
  }
  // Fallback to Supabase metadata
  const meta = (realtimeUser.value && realtimeUser.value.user_metadata) || (user && user.value && user.value.user_metadata);
  return meta && meta.role === 'Normal User';
}

function hasUserQuestions() {
  // Check if user has completed the onboarding questions
  const hasQuestions = userQuestions.value !== null && userQuestions.value !== '';
  return hasQuestions;
}

async function checkUserQuestionsFromBackend() {
  const { data } = await supabase.auth.getUser();
  
  if (data && data.user) {
    try {
      const response = await fetch('/api/check-user-questions', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          email: data.user.email
        })
      });
      
      if (response.ok) {
        const result = await response.json();
        
        userQuestions.value = result.user_questions;
        userRole.value = result.role;
        userQuestionsLoaded.value = true;
        
        return result.has_questions;
      } else if (response.status === 404) {
        // User not found in Laravel, try to sync from Supabase
        console.log('User not found in Laravel, attempting to sync from Supabase...');
        try {
          const syncResponse = await fetch('/api/sync-from-supabase-to-laravel', {
            method: 'POST',
            headers: { 
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              email: data.user.email
            })
          });
          
          if (syncResponse.ok) {
            const syncResult = await syncResponse.json();
            console.log('User synced successfully:', syncResult);
            
            // Retry the check after sync
            const retryResponse = await fetch('/api/check-user-questions', {
              method: 'POST',
              headers: { 
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                email: data.user.email
              })
            });
            
            if (retryResponse.ok) {
              const retryResult = await retryResponse.json();
              userQuestions.value = retryResult.user_questions;
              userRole.value = retryResult.role;
              userQuestionsLoaded.value = true;
              return retryResult.has_questions;
            }
          } else {
            console.error('Sync failed:', syncResponse.status, syncResponse.statusText);
          }
        } catch (syncError) {
          console.error('Sync error:', syncError);
        }
      } else {
        console.error('API response not ok:', response.status, response.statusText);
      }
    } catch (error) {
      console.error('API check error:', error);
    }
  }
  return false;
}





async function acceptRoleChange() {
  roleChangeLoading.value = true;
  roleChangeError.value = '';
  // 1. Update Supabase Auth metadata
  const { error } = await supabase.auth.updateUser({ data: { role: 'Caregiver' } });
  if (!error) {
    // 2. Sync backend
    const userData = (await supabase.auth.getUser()).data.user;
    if (userData) {
      await fetch('/api/sync-supabase-user', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: userData.id,
          email: userData.email,
          name: userData.user_metadata?.name || '',
          role: 'Caregiver',
        })
      });
      // 3. Refresh user data and UI
      const { data } = await supabase.auth.getUser();
      if (data && data.user) {
        realtimeUser.value = data.user;
      }
      showRoleChangeDialog.value = false;
    } else {
      roleChangeError.value = 'User not found.';
    }
  } else {
    roleChangeError.value = error.message || 'Failed to update role.';
  }
  roleChangeLoading.value = false;
}



onUnmounted(() => {
  if (pollingInterval) {
    clearInterval(pollingInterval);
    pollingInterval = null;
  }
  if (continuousPollingInterval) {
    clearInterval(continuousPollingInterval);
    continuousPollingInterval = null;
  }
});
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />
        <div v-if="loading" class="flex items-center justify-center h-96">
            <span class="text-lg text-gray-500">Loading session...</span>
        </div>
        <div v-else-if="user" class="p-6">
            <h1 class="text-2xl font-bold mb-4 flex items-center justify-between">
              <span>
                Welcome, <span class="text-gray-500">{{
                  realtimeUser && realtimeUser.user_metadata?.display_name ||
                  realtimeUser && realtimeUser.user_metadata?.name ||
                  user?.user_metadata?.display_name ||
                  user?.user_metadata?.name ||
                  user?.email ||
                  'User'
                }}!</span>
              </span>
              <button v-if="isNormalUser()" @click="showRoleChangeDialog = true" class="h-8 px-3 rounded-md bg-primary text-primary-foreground text-sm font-medium shadow-xs hover:bg-primary/90 transition-all">
                Add More Profiles
              </button>
            </h1>
            

            
            <!-- Professional, accessible modal dialog -->
            <div v-if="showRoleChangeDialog" class="fixed inset-0 flex items-center justify-center z-50 bg-black/30">
              <div class="bg-white rounded shadow-lg p-8 w-full max-w-md">
                <h2 class="text-lg font-semibold mb-4">Change Profile Role</h2>
                <p class="mb-4">To add more profiles, your account role will be changed from <b>Normal User</b> to <b>Caregiver</b>.<br/>Do you accept this change?</p>
                <div v-if="roleChangeError" class="text-red-600 mb-2">{{ roleChangeError }}</div>
                <div class="flex justify-end gap-2">
                  <button @click="showRoleChangeDialog = false" class="bg-gray-200 px-4 py-2 rounded text-sm">Cancel</button>
                  <button :disabled="roleChangeLoading" @click="acceptRoleChange" class="bg-green-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-green-700">
                    {{ roleChangeLoading ? 'Processing...' : 'Accept & Submit' }}
                  </button>
                </div>
              </div>
            </div>


            
            <div v-if="isNormalUser() && !userQuestionsLoaded" class="mb-8">
              <Card>
                <CardContent class="flex items-center justify-center py-8">
                  <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"></div>
                    <p class="text-sm text-gray-600">Checking your profile...</p>
                  </div>
                </CardContent>
              </Card>
            </div>
            
            <!-- Onboarding Card -->
            <div v-if="isNormalUser() && userQuestionsLoaded && !hasUserQuestions()" class="mb-8">
              <Card>
                <CardContent class="flex flex-col md:flex-row items-center justify-between gap-4 py-4">
                  <div class="flex-1 text-left">
                    <p class="text-base font-semibold mb-1">Set up your calling profile</p>
                    <p class="text-sm text-blue-800">To get started, please add your details using the form below. This helps us personalize your experience.</p>
                  </div>
                  <div class="flex items-center h-full">
                    <Button :disabled="onboardingLoading" @click="openOnboardingFormLink" size="sm" variant="default">
                      Add Details
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </div>
            <div v-if="!isNormalUser() || (userQuestionsLoaded && hasUserQuestions())">
              <!-- Restored Dashboard UI -->
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
                <Card v-for="stat in stats" :key="stat.title" class="h-auto flex flex-col justify-between p-4 min-h-20">
                  <CardHeader class="flex flex-row items-start justify-between pb-0 px-0 mb-0">
                    <div class="flex flex-col items-start flex-1">
                      <CardTitle class="text-sm font-medium mb-0 leading-tight">{{ stat.title }}</CardTitle>
                      <div class="text-2xl font-bold leading-tight mt-0 mb-0">{{ stat.value }}</div>
                      <div class="flex items-center gap-1 mt-0 text-xs font-medium"
                        :class="{
                          'text-green-600': stat.change > 0,
                          'text-red-600': stat.change < 0,
                          'text-muted-foreground': stat.change === 0
                        }">
                        <span v-if="stat.change > 0">▲</span>
                        <span v-else-if="stat.change < 0">▼</span>
                        <span>{{ Math.abs(stat.change) }}%</span>
                      </div>
                    </div>
                    <div :class="['rounded-xl', stat.iconBg, 'p-1.5', 'flex', 'items-center', 'justify-center', 'ml-2']">
                      <Icon :name="stat.icon" class="h-6 w-6" :class="stat.iconColor" />
                    </div>
                  </CardHeader>
                </Card>
              </div>
              <div class="flex flex-row gap-6 mb-8">
                <div class="w-[70%]">
                  <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-semibold">Usage Overview</h2>
                    <Link href="/elderly-profiles" class="px-6 py-1.5 bg-primary text-white rounded-full font-semibold hover:bg-primary/90 h-10 flex items-center">All Profiles</Link>
                  </div>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card v-for="profile in elderlyProfiles" :key="profile.name" class="flex flex-col items-center py-6">
                      <span class="mb-2 font-medium">{{ profile.name }}</span>
                      <VueUiWheel :dataset="{ percentage: profile.score }" :config="baseWheelConfig" />
                    </Card>
                  </div>
                </div>
                <div class="w-[30%]">
                  <Card>
                    <CardHeader>
                      <CardTitle>Subscription Overview</CardTitle>
                    </CardHeader>
                    <CardContent>
                      <VueUiTiremarks :dataset="{ percentage: 100 }" :config="tiremarksConfig" />
                    </CardContent>
                  </Card>
                </div>
              </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center h-96">
            <span class="text-lg text-red-500">Session error. Please log in again.</span>
        </div>
    </AppLayout>
</template>
