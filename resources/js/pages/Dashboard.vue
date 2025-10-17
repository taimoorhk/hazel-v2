<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { ref, onMounted, computed } from 'vue';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { supabase } from '@/lib/supabaseClient';
import type { User } from '@supabase/supabase-js';
import CleanWorkingDashboard from '@/components/CleanWorkingDashboard.vue';

const { loading } = useAuthGuard();
const { user } = useSupabaseUser();

// Add a ref for the latest user details
const realtimeUser = ref<User | null>(null);


onMounted(async () => {
  // Wait for session to be available
  const waitForSession = async () => {
    if (user.value) {
      // Fetch the latest user details from Supabase
      const { data } = await supabase.auth.getUser();
      if (data && data.user) {
        realtimeUser.value = data.user;
      }
      // Listen for user changes in realtime
      supabase.auth.onAuthStateChange((_event, newSession) => {
        if (newSession && newSession.user) {
          realtimeUser.value = newSession.user;
        }
      });
    } else {
      setTimeout(waitForSession, 500);
    }
  };
  
  await waitForSession();
});

function isNormalUser() {
  // Check the role from Supabase metadata
  const meta = (realtimeUser.value && realtimeUser.value.user_metadata) || (user && user.value && user.value.user_metadata);
  return meta && meta.role === 'Normal User';
}

function isCaregiverOrOrganization() {
  // Check the role from Supabase metadata
  const meta = (realtimeUser.value && realtimeUser.value.user_metadata) || (user && user.value && user.value.user_metadata);
  const role = meta?.role;
  
  // If no role is assigned, assume caregiver/organization for users who aren't explicitly "Normal User"
  if (!role) {
    return true; // Show button by default for users without explicit roles
  }
  
  return role === 'Caregiver' || role === 'Organization' || role === 'Admin';
}

function getUserAccountId() {
  // Get the current authenticated user
  const currentUser = realtimeUser.value || user.value;
  if (!currentUser?.email) {
    return null;
  }
  
  // Map email addresses to their respective account IDs
  const emailToAccountId = {
    'mtaimoorhas1@gmail.com': 6,
    'jsahib@gmail.com': 6, // This is an elderly profile under account 6
    'microassetsmain@gmail.com': 7, // New account ID for microassetsmain
    // Add more mappings as needed
  };
  
  return emailToAccountId[currentUser.email] || null;
}

function getUserProfileId() {
  // For normal users, we'll use their user ID as the profile ID
  const currentUser = realtimeUser.value || user.value;
  if (currentUser?.email) {
    // For main account holders, use profile ID -1
    // For elderly profiles, use their specific profile ID
    if (currentUser.email === 'jsahib@gmail.com') {
      return 15; // Elderly profile ID
    }
    return -1; // Main account holder profile ID
  }
  return -1; // Default fallback
}

</script>

<template>
  <AppLayout>
    <Head title="Dashboard" />
    
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
        <span class="text-lg text-[#2871B5]/70">Loading your dashboard...</span>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div v-else-if="user" class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">
              Welcome, {{ 
                realtimeUser?.user_metadata?.display_name ||
                realtimeUser?.user_metadata?.name ||
                user?.user_metadata?.display_name ||
                user?.user_metadata?.name ||
                user?.email?.split('@')[0] ||
                'User'
              }}!
            </h1>
            <p class="text-gray-600 mt-1">
              {{ isNormalUser() ? 'Your personal health dashboard' : 'Caregiver dashboard overview' }}
            </p>
          </div>
          
          <!-- Add Elderly Profile Button for Caregivers/Organizations -->
          <div v-if="isCaregiverOrOrganization()" class="flex-shrink-0">
            <button 
              @click="$inertia.visit('/elderly-profiles')"
              class="inline-flex items-center px-4 py-2 bg-[#2871B5] text-white text-sm font-medium rounded-lg hover:bg-[#1e5a8a] focus:outline-none focus:ring-2 focus:ring-[#2871B5] focus:ring-offset-2 transition-colors duration-200"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add Elderly Profile
            </button>
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
              {{ isNormalUser() ? 'Your comprehensive health insights and conversation analytics' : 'Health monitoring and analytics for all profiles' }}
            </p>
          </CardHeader>
          <CardContent>
    <CleanWorkingDashboard
      :profile-id="getUserProfileId()"
      :account-id="getUserAccountId()"
      :is-elderly-profile="false"
    />
          </CardContent>
        </Card>


        <!-- Quick Stats for Normal Users -->
        <div v-if="isNormalUser()" class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Card class="bg-gradient-to-r from-green-50 to-green-100 border-green-200">
            <CardContent class="p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-green-700">Health Status</p>
                  <p class="text-2xl font-bold text-green-800">Excellent</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-xl">💚</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-gradient-to-r from-blue-50 to-blue-100 border-blue-200">
            <CardContent class="p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-blue-700">Engagement</p>
                  <p class="text-2xl font-bold text-blue-800">High</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-xl">📈</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card class="bg-gradient-to-r from-purple-50 to-purple-100 border-purple-200">
            <CardContent class="p-4">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-purple-700">Mood</p>
                  <p class="text-2xl font-bold text-purple-800">Positive</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-xl">😊</span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="text-red-500 text-xl mb-2">⚠️</div>
        <span class="text-lg text-red-500">Session error. Please log in again.</span>
      </div>
    </div>
  </AppLayout>
</template>