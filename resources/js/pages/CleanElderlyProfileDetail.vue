<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { supabase } from '@/lib/supabaseClient';
import FinalWorkingDashboard from '@/components/FinalWorkingDashboard.vue';

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

onMounted(async () => {
  const profileId = page.props.id;
  console.log('🔍 CleanElderlyProfileDetail mounted with profileId:', profileId);
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
    <div v-else-if="profile" class="min-h-screen bg-gray-50">
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
              📊 Health Analytics
            </CardTitle>
            <p class="text-gray-600 text-sm">
              Real-time health insights for {{ profile.name }}
            </p>
          </CardHeader>
          <CardContent>
            <FinalWorkingDashboard 
              :profile-id="15" 
              :account-id="6"
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
