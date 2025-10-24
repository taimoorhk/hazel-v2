<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { supabase } from '@/lib/supabaseClient';
import CleanWorkingDashboard from '@/components/CleanWorkingDashboard.vue';

// Type definitions
interface ElderlyProfile {
  id: number;
  name: string;
  email: string;
  status: string;
  created_at: string;
  updated_at: string;
  associated_account_email?: string;
}


useAuthGuard();
const { user } = useSupabaseUser();
const page = usePage();

// Reactive data
const profile = ref<ElderlyProfile | null>(null);
const loading = ref(true);
const error = ref<string | null>(null);

// Function to get user email from various sources with comprehensive fallbacks
const getUserEmail = async (): Promise<string | null> => {
  console.log('🔍 Starting comprehensive user email detection...');
  
  // Method 1: Try useSupabaseUser composable
  let userEmail = user.value?.email;
  if (userEmail) {
    console.log('✅ Method 1 - useSupabaseUser:', userEmail);
    return userEmail;
  }
  console.log('❌ Method 1 - useSupabaseUser: No email found');

  // Method 2: Try direct Supabase session (most reliable on refresh)
  try {
    const { data: { session }, error } = await supabase.auth.getSession();
    if (error) {
      console.log('⚠️ Supabase session error:', error);
    } else if (session?.user?.email) {
      userEmail = session.user.email;
      console.log('✅ Method 2 - Direct Supabase session:', userEmail);
      return userEmail;
    }
  } catch (error) {
    console.log('⚠️ Could not get Supabase session:', error);
  }
  console.log('❌ Method 2 - Direct Supabase session: No email found');

  // Method 3: Try localStorage with multiple keys
  const localStorageKeys = [
    'sb-hazel-auth-token',
    'supabase.auth.token',
    'sb-auth-token',
    'supabase-session',
    'sb-hazel-auth-token',
    'supabase.auth.token'
  ];
  
  for (const key of localStorageKeys) {
    try {
      const storedData = localStorage.getItem(key);
      if (storedData) {
        const parsedData = JSON.parse(storedData);
        userEmail = parsedData?.user?.email || parsedData?.email || parsedData?.currentSession?.user?.email;
        if (userEmail) {
          console.log(`✅ Method 3 - localStorage (${key}):`, userEmail);
          return userEmail;
        }
      }
    } catch (e) {
      console.log(`⚠️ Could not parse localStorage key ${key}:`, e);
    }
  }
  console.log('❌ Method 3 - localStorage: No email found');

  // Method 4: Try to get from sessionStorage
  try {
    const sessionStorageKeys = ['supabase.auth.token', 'sb-hazel-auth-token'];
    for (const key of sessionStorageKeys) {
      const storedData = sessionStorage.getItem(key);
      if (storedData) {
        const parsedData = JSON.parse(storedData);
        userEmail = parsedData?.user?.email || parsedData?.email;
        if (userEmail) {
          console.log(`✅ Method 4 - sessionStorage (${key}):`, userEmail);
          return userEmail;
        }
      }
    }
  } catch (e) {
    console.log('⚠️ Could not check sessionStorage:', e);
  }
  console.log('❌ Method 4 - sessionStorage: No email found');

  console.log('⚠️ No user email found from any source');
  return null;
};

// Function to determine account ID from email
const getAccountIdFromEmail = (email: string | null): number => {
  if (!email) {
    console.log('⚠️ No user email provided, using default account ID 6');
    return 6;
  }

  const emailToAccountId: Record<string, number> = {
    'mtaimoorhas1@gmail.com': 6,
    'jsahib@gmail.com': 6, // This is an elderly profile under account 6
    'microassetsmain@gmail.com': 7, // Account ID for microassetsmain
    'microassetsprofile1@gmail.com': 7, // Elderly profile under account 7
    // Add more mappings as needed
  };

  const detectedAccountId = emailToAccountId[email] || 6;
  console.log('📊 Account ID mapping:', email, '->', detectedAccountId);
  return detectedAccountId;
};

// Reactive account ID with async detection
const accountId = ref(6); // Default fallback
const accountIdLoading = ref(true); // Track if we're still determining account ID

// Function to update account ID with comprehensive detection
const updateAccountId = async () => {
  try {
    console.log('🔄 Updating account ID...');
    const userEmail = await getUserEmail();
    const detectedId = getAccountIdFromEmail(userEmail);
    
    if (accountId.value !== detectedId) {
      console.log('🔄 Account ID updated:', accountId.value, '->', detectedId);
      accountId.value = detectedId;
    } else {
      console.log('✅ Account ID unchanged:', detectedId);
    }
    
    accountIdLoading.value = false;
  } catch (error) {
    console.error('⚠️ Error updating account ID:', error);
    accountIdLoading.value = false;
  }
};

const fetchProfileDetails = async (profileId: string) => {
  try {
    loading.value = true;
    error.value = null;

    // Fetch elderly profile from Supabase
    const { data, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('id', profileId)
      .single();

    if (fetchError) {
      throw fetchError;
    }

    profile.value = data;
  } catch (err) {
    console.error('Error fetching profile details:', err);
    error.value = err instanceof Error ? err.message : 'Failed to fetch profile details';
  } finally {
    loading.value = false;
  }
};


// Watch for user changes and update account ID accordingly
watch(user, async (newUser, oldUser) => {
  console.log('👤 User changed:', { oldUser, newUser });
  if (newUser?.email !== oldUser?.email) {
    console.log('🔄 User email changed, recomputing account ID');
    accountIdLoading.value = true;
    await updateAccountId();
  }
}, { deep: true });

// Watch for account ID changes and log them
watch(accountId, (newAccountId, oldAccountId) => {
  console.log('🔄 Account ID changed:', { oldAccountId, newAccountId });
  console.log('📊 Current account ID:', newAccountId);
}, { immediate: true });

// Watch for account ID loading state
watch(accountIdLoading, (loading) => {
  console.log('🔄 Account ID loading state:', loading);
});

onMounted(async () => {
  const profileId = page.props.id;
  console.log('🔍 ElderlyProfileDetail mounted with profileId:', profileId);
  console.log('👤 Current user on mount (from composable):', user.value);
  
  // Immediately update account ID with comprehensive detection
  console.log('🔄 Starting immediate account ID detection...');
  await updateAccountId();
  console.log('📊 Final account ID on mount:', accountId.value);
  
  // Set up periodic account ID verification (every 3 seconds for more frequent checks)
  const accountIdCheckInterval = setInterval(async () => {
    console.log('⏰ Periodic account ID check...');
    await updateAccountId();
  }, 3000); // Check every 3 seconds
  
  // Clean up interval on unmount
  onUnmounted(() => {
    if (accountIdCheckInterval) {
      clearInterval(accountIdCheckInterval);
      console.log('🧹 Cleared periodic account ID check interval');
    }
  });
  
  if (profileId) {
    await fetchProfileDetails(profileId as string);
  }
});
</script>

<template>
  <AppLayout>
    <Head :title="profile ? `${profile.name} - Profile Details` : 'Elderly Profile Details'" />

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
        <span class="text-lg text-[#2871B5]/70">Loading profile details...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="text-red-500 text-xl mb-2">⚠️</div>
        <span class="text-lg text-red-500">{{ error }}</span>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="profile" class="min-h-screen" style="background-color: #f9fafb;">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-6">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-gradient-to-r from-[#2871B5] to-blue-600 rounded-full flex items-center justify-center">
            <span class="text-white font-bold text-2xl">{{ profile.name.charAt(0) }}</span>
          </div>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ profile.name }}</h1>
            <p class="text-gray-600">{{ profile.email }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="profile.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ profile.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div class="p-6">

        <!-- Health Analytics Dashboard -->
        <Card class="mb-6">
          <CardHeader>
            <CardTitle class="text-xl font-semibold text-[#2871B5] flex items-center gap-2">
              📊 Health Analytics Dashboard
            </CardTitle>
            <p class="text-gray-600 text-sm">
              Comprehensive health insights and conversation analytics for {{ profile.name }}
            </p>
            <div class="mt-2 text-xs text-gray-500">
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                Account ID: {{ accountIdLoading ? 'Detecting...' : accountId }}
              </span>
              <span class="bg-green-100 text-green-800 px-2 py-1 rounded ml-2">Profile ID: {{ profile.id }}</span>
              <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded ml-2">
                User: {{ user?.email || 'Detecting...' }}
              </span>
            </div>
          </CardHeader>
          <CardContent>
            <!-- Show loading state while determining account ID -->
            <div v-if="accountIdLoading" class="flex items-center justify-center p-8">
              <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
                <span class="text-lg text-[#2871B5]/70">Detecting account information...</span>
              </div>
            </div>
            
            <!-- Show dashboard only when account ID is determined -->
            <CleanWorkingDashboard 
              v-else
              :profile-id="profile.id" 
              :account-id="accountId"
              :is-elderly-profile="true"
            />
          </CardContent>
        </Card>

      </div>
    </div>

    <!-- Profile Not Found -->
    <div v-else class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="text-gray-500 text-xl mb-2">👤</div>
        <span class="text-lg text-gray-500">Profile not found</span>
      </div>
    </div>
  </AppLayout>
</template>