<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref, onMounted, computed, watch } from 'vue';
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

// Get account ID based on logged-in user
const getAccountId = () => {
  const currentUser = user.value;
  console.log('🔍 Current user for account ID detection:', currentUser);
  
  // Try to get user email from multiple sources
  let userEmail = currentUser?.email;
  
  // Fallback: try to get from localStorage if Supabase user isn't available
  if (!userEmail) {
    try {
      const storedUser = localStorage.getItem('sb-hazel-auth-token');
      if (storedUser) {
        const parsedUser = JSON.parse(storedUser);
        userEmail = parsedUser?.user?.email;
        console.log('🔍 Fallback user email from localStorage:', userEmail);
      }
    } catch (e) {
      console.log('⚠️ Could not parse stored user data');
    }
  }
  
  // Additional fallback: try to get from Supabase session
  if (!userEmail) {
    try {
      const session = localStorage.getItem('sb-hazel-auth-token');
      if (session) {
        const sessionData = JSON.parse(session);
        userEmail = sessionData?.user?.email;
        console.log('🔍 Fallback user email from session:', userEmail);
      }
    } catch (e) {
      console.log('⚠️ Could not parse session data');
    }
  }
  
  if (!userEmail) {
    console.log('⚠️ No user email found, using default account ID 6');
    return 6; // Default fallback
  }
  
  const emailToAccountId: Record<string, number> = {
    'mtaimoorhas1@gmail.com': 6,
    'jsahib@gmail.com': 6, // This is an elderly profile under account 6
    'microassetsmain@gmail.com': 7, // Account ID for microassetsmain
    // Add more mappings as needed
  };
  
  const detectedAccountId = emailToAccountId[userEmail] || 6;
  console.log('📊 Detected account ID:', detectedAccountId, 'for user:', userEmail);
  
  return detectedAccountId;
};

// Computed account ID with reactive updates
const accountId = computed(() => {
  const detectedId = getAccountId();
  console.log('🔄 Computed account ID:', detectedId);
  return detectedId;
});

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
watch(user, (newUser, oldUser) => {
  console.log('👤 User changed:', { oldUser, newUser });
  if (newUser?.email !== oldUser?.email) {
    console.log('🔄 User email changed, recomputing account ID');
    // The computed accountId will automatically update
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
  console.log('📊 Account ID on mount:', accountId.value);
  
  // Additional check: try to get user from Supabase session directly
  try {
    const { data: { session } } = await supabase.auth.getSession();
    if (session?.user?.email) {
      console.log('🔍 Direct Supabase session user:', session.user.email);
      // Force recomputation of account ID
      const newAccountId = getAccountId();
      console.log('📊 Recalculated account ID after session check:', newAccountId);
    }
  } catch (error) {
    console.log('⚠️ Could not get Supabase session:', error);
  }
  
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