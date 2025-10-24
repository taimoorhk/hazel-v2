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

// Get account ID based on logged-in user with multiple detection methods
const getAccountId = async () => {
  console.log('🔍 Starting account ID detection...');
  
  // Method 1: Try useSupabaseUser composable
  let userEmail = user.value?.email;
  console.log('🔍 Method 1 - useSupabaseUser:', userEmail);
  
  // Method 2: Try localStorage with multiple keys
  if (!userEmail) {
    const localStorageKeys = [
      'sb-hazel-auth-token',
      'supabase.auth.token',
      'sb-auth-token',
      'supabase-session'
    ];
    
    for (const key of localStorageKeys) {
      try {
        const storedData = localStorage.getItem(key);
        if (storedData) {
          const parsedData = JSON.parse(storedData);
          userEmail = parsedData?.user?.email || parsedData?.email;
          if (userEmail) {
            console.log(`🔍 Method 2 - localStorage (${key}):`, userEmail);
            break;
          }
        }
      } catch (e) {
        console.log(`⚠️ Could not parse localStorage key ${key}:`, e);
      }
    }
  }
  
  // Method 3: Try direct Supabase session
  if (!userEmail) {
    try {
      const { data: { session } } = await supabase.auth.getSession();
      userEmail = session?.user?.email;
      console.log('🔍 Method 3 - Direct Supabase session:', userEmail);
    } catch (error) {
      console.log('⚠️ Could not get Supabase session:', error);
    }
  }
  
  // Method 4: Try to get from document.cookie or other sources
  if (!userEmail) {
    try {
      // Check if there's any user info in cookies or other storage
      const cookies = document.cookie.split(';');
      for (const cookie of cookies) {
        if (cookie.includes('user') || cookie.includes('email')) {
          console.log('🔍 Method 4 - Cookie check:', cookie);
        }
      }
    } catch (e) {
      console.log('⚠️ Could not check cookies:', e);
    }
  }
  
  if (!userEmail) {
    console.log('⚠️ No user email found after all methods, using default account ID 6');
    return 6; // Default fallback
  }
  
  const emailToAccountId: Record<string, number> = {
    'mtaimoorhas1@gmail.com': 6,
    'jsahib@gmail.com': 6, // This is an elderly profile under account 6
    'microassetsmain@gmail.com': 7, // Account ID for microassetsmain
    // Add more mappings as needed
  };
  
  const detectedAccountId = emailToAccountId[userEmail] || 6;
  console.log('📊 Final detected account ID:', detectedAccountId, 'for user:', userEmail);
  
  return detectedAccountId;
};

// Reactive account ID with async detection
const accountId = ref(6); // Default fallback

// Function to update account ID
const updateAccountId = async () => {
  try {
    const detectedId = await getAccountId();
    if (accountId.value !== detectedId) {
      console.log('🔄 Account ID updated:', accountId.value, '->', detectedId);
      accountId.value = detectedId;
    }
  } catch (error) {
    console.error('⚠️ Error updating account ID:', error);
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
    await updateAccountId();
  }
}, { deep: true });

// Watch for account ID changes and log them
watch(accountId, (newAccountId, oldAccountId) => {
  console.log('🔄 Account ID changed:', { oldAccountId, newAccountId });
}, { immediate: true });

onMounted(async () => {
  const profileId = page.props.id;
  console.log('🔍 ElderlyProfileDetail mounted with profileId:', profileId);
  console.log('👤 Current user on mount:', user.value);
  
  // Update account ID with comprehensive detection
  await updateAccountId();
  console.log('📊 Final account ID on mount:', accountId.value);
  
  // Set up periodic account ID verification (every 5 seconds)
  const accountIdCheckInterval = setInterval(async () => {
    console.log('🔄 Periodic account ID check...');
    await updateAccountId();
  }, 5000);
  
  // Clean up interval on unmount
  onUnmounted(() => {
    clearInterval(accountIdCheckInterval);
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
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">Account ID: {{ accountId }}</span>
              <span class="bg-green-100 text-green-800 px-2 py-1 rounded ml-2">Profile ID: {{ profile.id }}</span>
              <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded ml-2">User: {{ user?.email || 'Unknown' }}</span>
            </div>
          </CardHeader>
          <CardContent>
            <CleanWorkingDashboard 
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