<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
import { router as inertiaRouter } from '@inertiajs/vue3';
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
}

useAuthGuard();
const { user } = useSupabaseUser();

// Reactive data
const profiles = ref<ElderlyProfile[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const search = ref('');
const roleFilter = ref('');
const registrationFilter = ref('');
const activityFilter = ref('');
const statusFilter = ref('');
const deactivatingProfile = ref<number | null>(null);
const openDropdown = ref<number | null>(null);

// Realtime subscription
let realtimeChannel: any = null;

onMounted(async () => {
  const meta = user.value?.user_metadata;
  if (meta && meta.role === 'Normal User') {
    inertiaRouter.visit('/dashboard');
    return;
  }

  // Wait for user to be loaded
  if (!user.value) {
    console.log('Waiting for user to be loaded...');
    // Wait a bit for the user to be loaded
    await new Promise(resolve => setTimeout(resolve, 1000));
  }

  await fetchElderlyProfiles();
  setupRealtimeSubscription();
  
  // Add click outside listener
  document.addEventListener('click', handleClickOutside);
});

// Watch for user changes and fetch profiles when user is available
watch(user, async (newUser) => {
  if (newUser && !loading.value) {
    console.log('User loaded, fetching profiles...');
    await fetchElderlyProfiles();
  }
}, { immediate: false });

onUnmounted(() => {
  if (realtimeChannel) {
    supabase.removeChannel(realtimeChannel);
  }
  document.removeEventListener('click', handleClickOutside);
});

const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement;
  if (!target.closest('.dropdown-container')) {
    openDropdown.value = null;
  }
};

const toggleDropdown = (profileId: number, event: Event) => {
  event.stopPropagation();
  openDropdown.value = openDropdown.value === profileId ? null : profileId;
};

const setupRealtimeSubscription = () => {
  realtimeChannel = supabase
    .channel('elderly_profiles_changes')
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'public',
        table: 'elderly_profiles'
      },
      async (payload) => {
        console.log('Realtime change detected:', payload);
        // Refresh the data when changes occur
        await fetchElderlyProfiles();
      }
    )
    .subscribe();
};

const fetchElderlyProfiles = async () => {
  try {
    loading.value = true;
    error.value = null;

    // Get current user's email from Supabase
    const currentUserEmail = user.value?.email;
    console.log('Current user email:', currentUserEmail);
    console.log('Current user object:', user.value);
    
    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    // Wait for Supabase to be ready
    const { data: { session } } = await supabase.auth.getSession();
    console.log('Supabase session:', session);
    
    if (!session) {
      throw new Error('No active Supabase session');
    }

    console.log('Supabase session found, fetching profiles for:', currentUserEmail);

    // Try to fetch profiles with authentication
    let { data, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('associated_account_email', currentUserEmail)
      .order('created_at', { ascending: false });

    console.log('Supabase query result:', { data, error: fetchError });

    // If there's an RLS error, try without the filter to see if it's a policy issue
    if (fetchError && fetchError.code === 'PGRST116') {
      console.log('RLS policy error detected, trying alternative approach...');
      
      // Try to get all profiles and filter client-side
      const { data: allData, error: allError } = await supabase
        .from('elderly_profiles')
        .select('*')
        .order('created_at', { ascending: false });
      
      if (allError) {
        throw allError;
      }
      
      // Filter client-side
      data = allData?.filter(profile => profile.associated_account_email === currentUserEmail) || [];
      fetchError = null;
      console.log('Client-side filtered data:', data);
    }

    if (fetchError) {
      console.error('Supabase fetch error:', fetchError);
      throw fetchError;
    }

    console.log('Profiles found:', data?.length || 0);

    // Transform the data to match the expected format
    profiles.value = (data || []).map(profile => ({
      id: profile.id,
      name: profile.name || profile.email.split('@')[0], // Use name field, fallback to email prefix
      email: profile.email,
      avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(profile.name || profile.email.split('@')[0])}&background=random&size=128`,
      lastActivity: getLastActivityText(profile.updated_at),
      lastActivityType: getLastActivityType(profile.updated_at),
      registered: formatDate(profile.created_at),
      role: profile.temporary_role || 'elderly user',
      phone: profile.phone,
      status: profile.status || 'active',
      created_at: profile.created_at,
      updated_at: profile.updated_at
    }));

    console.log('Transformed profiles:', profiles.value);

  } catch (err) {
    console.error('Error fetching elderly profiles:', err);
    error.value = err instanceof Error ? err.message : 'Failed to load elderly profiles';
  } finally {
    loading.value = false;
  }
};

const deactivateProfile = async (profileId: number) => {
  try {
    deactivatingProfile.value = profileId;
    openDropdown.value = null; // Close dropdown
    
    const response = await fetch(`/api/elderly-profiles/${profileId}/deactivate`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    });

    const result = await response.json();

    if (result.success) {
      // Update the profile status locally
      const profileIndex = profiles.value.findIndex(p => p.id === profileId);
      if (profileIndex !== -1) {
        profiles.value[profileIndex].status = 'deactivated';
      }
      
      // Show success message
      alert('Profile deactivated successfully!');
    } else {
      throw new Error(result.message || 'Failed to deactivate profile');
    }
  } catch (err) {
    console.error('Error deactivating profile:', err);
    alert(err instanceof Error ? err.message : 'Failed to deactivate profile');
  } finally {
    deactivatingProfile.value = null;
  }
};

const getLastActivityText = (updatedAt: string) => {
  const now = new Date();
  const updated = new Date(updatedAt);
  const diffInHours = (now.getTime() - updated.getTime()) / (1000 * 60 * 60);
  
  if (diffInHours < 1) return 'Just now';
  if (diffInHours < 24) return `${Math.floor(diffInHours)}h ago`;
  if (diffInHours < 168) return `${Math.floor(diffInHours / 24)}d ago`;
  return 'Long ago';
};

const getLastActivityType = (updatedAt: string) => {
  const now = new Date();
  const updated = new Date(updatedAt);
  const diffInHours = (now.getTime() - updated.getTime()) / (1000 * 60 * 60);
  
  return diffInHours < 24 ? 'recent' : 'old';
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};

const clearFilters = () => {
  search.value = '';
  roleFilter.value = '';
  registrationFilter.value = '';
  activityFilter.value = '';
  statusFilter.value = '';
};

const openAddUserForm = () => {
  window.open('https://form.fillout.com/t/5WbxT5J6gxus', '_blank');
};

const createElderlyProfile = async (profileData: { name: string; email: string; phone?: string }) => {
  try {
    const currentUserEmail = user.value?.email;
    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    const { data, error } = await supabase
      .from('elderly_profiles')
      .insert([
        {
          name: profileData.name,
          email: profileData.email,
          phone: profileData.phone || null,
          temporary_role: 'elderly user',
          status: 'active',
          associated_account_email: currentUserEmail
        }
      ])
      .select();

    if (error) {
      throw error;
    }

    // Refresh the profiles list
    await fetchElderlyProfiles();

    return data[0];
  } catch (err) {
    console.error('Error creating elderly profile:', err);
    throw err;
  }
};

// Computed properties for filtering
const filteredProfiles = computed(() => {
  let filtered = profiles.value;

  // Search filter
  if (search.value) {
    const searchTerm = search.value.toLowerCase();
    filtered = filtered.filter(profile =>
      profile.name.toLowerCase().includes(searchTerm) ||
      profile.email.toLowerCase().includes(searchTerm) ||
      profile.role.toLowerCase().includes(searchTerm)
    );
  }

  // Role filter
  if (roleFilter.value) {
    filtered = filtered.filter(profile => profile.role === roleFilter.value);
  }

  // Status filter
  if (statusFilter.value) {
    filtered = filtered.filter(profile => profile.status === statusFilter.value);
  }

  // Registration filter
  if (registrationFilter.value) {
    const now = new Date();
    const oneMonthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
    
    filtered = filtered.filter(profile => {
      const createdDate = new Date(profile.created_at);
      if (registrationFilter.value === 'recent') {
        return createdDate >= oneMonthAgo;
      } else if (registrationFilter.value === 'old') {
        return createdDate < oneMonthAgo;
      }
      return true;
    });
  }

  // Activity filter
  if (activityFilter.value) {
    const now = new Date();
    const oneWeekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
    
    filtered = filtered.filter(profile => {
      const updatedDate = new Date(profile.updated_at);
      if (activityFilter.value === 'recent') {
        return updatedDate >= oneWeekAgo;
      } else if (activityFilter.value === 'old') {
        return updatedDate < oneWeekAgo;
      }
      return true;
    });
  }

  return filtered;
});
</script>

<template>
  <AppLayout>
    <Head title="Elderly Profiles" />
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-6">Elderly Profiles</h1>
      
      <!-- Compact Single Line Filters -->
      <div class="flex items-center gap-2 mb-6 w-full">
        <!-- Filters -->
        <select v-model="roleFilter" class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-black focus:border-black min-w-[90px] h-8">
          <option value="">Role</option>
          <option value="elderly user">Elderly User</option>
        </select>
        
        <select v-model="statusFilter" class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-black focus:border-black min-w-[85px] h-8">
          <option value="">Status</option>
          <option value="active">Active</option>
          <option value="deactivated">Deactivated</option>
        </select>
        
        <select v-model="registrationFilter" class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-black focus:border-black min-w-[110px] h-8">
          <option value="">Registration</option>
          <option value="recent">Recent</option>
          <option value="old">Old</option>
        </select>
        
        <select v-model="activityFilter" class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-black focus:border-black min-w-[95px] h-8">
          <option value="">Activity</option>
          <option value="recent">Recent</option>
          <option value="old">Old</option>
        </select>
        
        <button @click="clearFilters" class="rounded-lg border border-gray-300 px-2.5 py-1.5 text-xs flex items-center gap-1 bg-white hover:bg-gray-50 hover:border-gray-400 min-w-[70px] h-8 transition-colors">
          <span>Clear</span>
          <span class="text-sm">&times;</span>
        </button>
        
        <!-- Spacer -->
        <div class="flex-1"></div>
        
        <!-- Search and Actions -->
        <input v-model="search" type="text" placeholder="Search profiles..." class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs w-48 focus:outline-none focus:ring-1 focus:ring-black focus:border-black h-8" />
        
        <button class="rounded-lg border border-gray-300 px-2 py-1.5 flex items-center justify-center bg-white hover:bg-gray-50 hover:border-gray-400 h-8 w-8 transition-colors">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="2" rx="1" fill="currentColor"/><rect x="3" y="11" width="18" height="2" rx="1" fill="currentColor"/><rect x="3" y="17" width="18" height="2" rx="1" fill="currentColor"/></svg>
        </button>
        
        <button @click="openAddUserForm" class="rounded-lg bg-gradient-to-r from-black to-neutral-800 text-white px-4 py-1.5 flex items-center gap-1.5 text-xs font-medium hover:from-neutral-800 hover:to-black whitespace-nowrap h-8 transition-colors">
          Add user
          <span class="text-sm">+</span>
        </button>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
          <p class="text-gray-600">Loading elderly profiles...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="flex items-center justify-center py-12">
        <div class="text-center">
          <p class="text-red-600 mb-4">{{ error }}</p>
          <button @click="fetchElderlyProfiles" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">
            Try Again
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredProfiles.length === 0" class="flex items-center justify-center py-12">
        <div class="text-center">
          <p class="text-gray-600 mb-4">No elderly profiles found</p>
          <button @click="openAddUserForm" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">
            Add First Profile
          </button>
        </div>
      </div>

      <!-- Profiles Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="profile in filteredProfiles" :key="profile.id" class="rounded-xl border border-neutral-200 bg-white shadow-sm p-6 relative">
          <!-- Status Badge -->
          <div class="absolute top-4 right-4">
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-medium',
              profile.status === 'active' 
                ? 'bg-green-100 text-green-800' 
                : 'bg-red-100 text-red-800'
            ]">
              {{ profile.status === 'active' ? 'Active' : 'Deactivated' }}
            </span>
          </div>
          
          <div class="flex items-center gap-4 mb-4">
            <img :src="profile.avatar" alt="avatar" class="w-14 h-14 rounded-full object-cover border border-black" />
            <div class="flex-1">
              <div class="font-semibold text-lg">{{ profile.name }}</div>
              <div class="text-neutral-500 text-sm">{{ profile.email }}</div>
            </div>
            <div class="relative dropdown-container">
              <button 
                @click="toggleDropdown(profile.id, $event)"
                class="text-black hover:text-white hover:bg-gradient-to-r hover:from-black hover:to-neutral-800 p-2 rounded-full transition-colors"
              >
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="19" r="1.5" fill="currentColor"/></svg>
              </button>
              <!-- Dropdown menu for actions -->
              <div 
                v-if="openDropdown === profile.id"
                class="absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg py-1 z-10 min-w-[120px]"
              >
                <button 
                  v-if="profile.status === 'active'"
                  @click="deactivateProfile(profile.id)"
                  :disabled="deactivatingProfile === profile.id"
                  class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <span v-if="deactivatingProfile === profile.id">Deactivating...</span>
                  <span v-else>Deactivate</span>
                </button>
              </div>
            </div>
          </div>
          <hr class="my-4 border-black/10" />
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="text-neutral-400">Last Activity</div>
              <div class="flex items-center gap-1">
                <span :class="['h-2 w-2 rounded-full mr-1', profile.lastActivityType === 'recent' ? 'bg-black' : 'bg-black/30']"></span>
                <span :class="profile.lastActivityType === 'recent' ? 'text-black' : 'text-black/50'">{{ profile.lastActivity }}</span>
              </div>
            </div>
            <div>
              <div class="text-neutral-400">Registered</div>
              <div>{{ profile.registered }}</div>
            </div>
            <div>
              <span class="px-4 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                {{ profile.role }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 