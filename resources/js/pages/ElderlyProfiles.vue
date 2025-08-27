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
  associated_account_email?: string;
  user_questions?: string;
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

const openDropdown = ref<number | null>(null);

// Modal states
const showAddModal = ref(false);
const showEditModal = ref(false);
const showPersonalisationModal = ref(false);
const currentProfile = ref<ElderlyProfile | null>(null);
const isSaving = ref(false);
const isPersonalising = ref(false);

// Navigation function
const openProfileDetail = (profileId: number) => {
  window.location.href = `/elderly-profiles/${profileId}`;
};

// Form data
const form = ref({
  name: '',
  email: '',
  phone: '',
  status: 'active',
  associated_account_email: '',
});

// Personalisation form data
const personalisationForm = ref({
  user_questions: '',
});

// Form validation
const formErrors = ref<Record<string, string>>({});

// Realtime subscription
let realtimeChannel: any = null;
let periodicSyncInterval: NodeJS.Timeout | null = null;

onMounted(async () => {
  // Wait for user to be loaded
  if (!user.value) {
    console.log('Waiting for user to be loaded...');
    // Wait a bit for the user to be loaded
    await new Promise(resolve => setTimeout(resolve, 1000));
  }

  await fetchElderlyProfiles();
  setupRealtimeSubscription();
  startPeriodicSync();
  
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
  stopPeriodicSync();
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

// Modal management
const openAddUserForm = async () => {
  resetForm();
  // Get current user email from Supabase session
  const { data: { session } } = await supabase.auth.getSession();
  const currentUserEmail = session?.user?.email;
  form.value.associated_account_email = currentUserEmail || '';
  showAddModal.value = true;
};

const openEditForm = (profile: ElderlyProfile) => {
  currentProfile.value = profile;
  form.value = {
    name: profile.name || '',
    email: profile.email,
    phone: profile.phone || '',
    status: profile.status || 'active',
    associated_account_email: profile.associated_account_email || '',
  };
  showEditModal.value = true;
};

const openPersonalisationForm = (profile: ElderlyProfile) => {
  currentProfile.value = profile;
  personalisationForm.value = {
    user_questions: profile.user_questions || '',
  };
  showPersonalisationModal.value = true;
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  showPersonalisationModal.value = false;
  currentProfile.value = null;
  resetForm();
};

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    phone: '',
    status: 'active',
    associated_account_email: '',
  };
  formErrors.value = {};
};

// Profile operations
const saveProfile = async () => {
  try {
    isSaving.value = true;
    formErrors.value = {};

    // Get current user email from Supabase session
    const { data: { session } } = await supabase.auth.getSession();
    const currentUserEmail = session?.user?.email;
    
    console.log('Current user email:', currentUserEmail);
    console.log('Form data:', form.value);

    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    // Add the associated account email to the form data
    const profileData = {
      ...form.value,
      associated_account_email: currentUserEmail,
      updated_at: new Date().toISOString()
    };

    let result;
    
    if (currentProfile.value) {
      // Update existing profile - preserve all existing fields and only update changed ones
      console.log('Updating profile:', currentProfile.value.id);
      
      // First, get the current profile data to preserve existing fields
      const { data: existingProfile, error: fetchError } = await supabase
        .from('elderly_profiles')
        .select('*')
        .eq('id', currentProfile.value.id)
        .single();
      
      if (fetchError) throw fetchError;
      
      // Merge existing data with form data, preserving all existing fields
      const updateData = {
        ...existingProfile, // Keep all existing fields (story, created_at, etc.)
        ...profileData, // Override with form data
        updated_at: new Date().toISOString() // Update timestamp
      };
      
      console.log('Preserving existing data:', existingProfile);
      console.log('Update data:', updateData);
      
      const { data, error } = await supabase
        .from('elderly_profiles')
        .update(updateData)
        .eq('id', currentProfile.value.id)
        .eq('associated_account_email', currentUserEmail) // Security check
        .select()
        .single();
      
      if (error) throw error;
      result = data;
    } else {
      // Create new profile
      console.log('Creating new profile');
      const { data, error } = await supabase
        .from('elderly_profiles')
        .insert([profileData])
        .select()
        .single();
      
      if (error) throw error;
      result = data;
    }

    console.log('Profile saved successfully:', result);
    
    // Success - close modal and refresh
    closeModal();
    await fetchElderlyProfiles();
    
  } catch (error: any) {
    console.error('Error saving profile:', error);
    formErrors.value = { general: error.message || 'An error occurred while saving' };
  } finally {
    isSaving.value = false;
  }
};

const updateProfileStatus = async (profile: ElderlyProfile, newStatus: string) => {
  try {
    // Get current user email from Supabase session
    const { data: { session } } = await supabase.auth.getSession();
    const currentUserEmail = session?.user?.email;
    
    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    console.log('Updating profile status:', profile.id, 'to', newStatus);

    // First, get the current profile data to preserve existing fields
    const { data: existingProfile, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('id', profile.id)
      .single();
    
    if (fetchError) throw fetchError;
    
    // Only update the status field, preserve everything else
    const updateData = {
      ...existingProfile, // Keep all existing fields (story, created_at, etc.)
      status: newStatus,
      updated_at: new Date().toISOString()
    };
    
    console.log('Preserving existing data for status update:', existingProfile);
    console.log('Status update data:', updateData);

    const { data, error } = await supabase
      .from('elderly_profiles')
      .update(updateData)
      .eq('id', profile.id)
      .eq('associated_account_email', currentUserEmail) // Security check
      .select()
      .single();

    if (error) throw error;

    console.log('Profile status updated successfully:', data);
    await fetchElderlyProfiles(); // Refresh the list
    
  } catch (error: any) {
    console.error('Error updating status:', error);
    // You could show a toast notification here
  }
};

const savePersonalisation = async () => {
  try {
    isPersonalising.value = true;
    
    if (!currentProfile.value) {
      throw new Error('No profile selected');
    }

    // Get current user email from Supabase session
    const { data: { session } } = await supabase.auth.getSession();
    const currentUserEmail = session?.user?.email;
    
    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    console.log('Saving personalisation for profile:', currentProfile.value.id);

    // First, get the current profile data to preserve existing fields
    const { data: existingProfile, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('id', currentProfile.value.id)
      .single();
    
    if (fetchError) throw fetchError;
    
    // Update only the user_questions field, preserve everything else
    const updateData = {
      ...existingProfile, // Keep all existing fields
      user_questions: personalisationForm.value.user_questions,
      updated_at: new Date().toISOString()
    };
    
    console.log('Preserving existing data for personalisation update:', existingProfile);
    console.log('Personalisation update data:', updateData);

    // Update the elderly_profiles table
    const { data, error } = await supabase
      .from('elderly_profiles')
      .update(updateData)
      .eq('id', currentProfile.value.id)
      .eq('associated_account_email', currentUserEmail) // Security check
      .select()
      .single();

    if (error) throw error;

    console.log('Personalisation saved to elderly_profiles successfully:', data);
    
    // Also update the user_questions in the public.users table for proper sync
    try {
      const { error: usersError } = await supabase
        .from('users')
        .update({ 
          user_questions: personalisationForm.value.user_questions,
          updated_at: new Date().toISOString()
        })
        .eq('email', currentUserEmail);
      
      if (usersError) {
        console.warn('Could not update public.users user_questions:', usersError);
      } else {
        console.log('Public users table updated successfully');
      }
    } catch (usersError) {
      console.warn('Error updating public.users table:', usersError);
    }
    
    // Also update the user_questions in the auth.users table for real-time sync
    try {
      const { error: authError } = await supabase.auth.updateUser({
        data: { user_questions: personalisationForm.value.user_questions }
      });
      
      if (authError) {
        console.warn('Could not update auth user_questions:', authError);
      } else {
        console.log('Auth user_questions updated successfully');
      }
    } catch (authError) {
      console.warn('Error updating auth user_questions:', authError);
    }
    
    // Success - close modal and refresh
    closeModal();
    await fetchElderlyProfiles();
    
  } catch (error: any) {
    console.error('Error saving personalisation:', error);
    // You could show a toast notification here
  } finally {
    isPersonalising.value = false;
  }
};

const setupRealtimeSubscription = () => {
  // Create a comprehensive real-time subscription channel
  realtimeChannel = supabase
    .channel('comprehensive_sync_channel')
    
    // Listen to elderly_profiles table changes
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'public',
        table: 'elderly_profiles'
      },
      async (payload) => {
        console.log('🔄 Elderly profiles change detected:', payload);
        await fetchElderlyProfiles();
      }
    )
    
    // Listen to public.users table changes (for user_questions updates)
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'public',
        table: 'users'
      },
      async (payload) => {
        console.log('🔄 Users table change detected:', payload);
        // Check if this change affects the current user
        if (payload.new && typeof payload.new === 'object' && 'email' in payload.new && payload.new.email === user.value?.email) {
          console.log('🔄 Current user data changed, refreshing profiles...');
          await fetchElderlyProfiles();
        }
      }
    )
    
    // Listen to auth.users changes (for user_metadata updates)
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'auth',
        table: 'users'
      },
      async (payload) => {
        console.log('🔄 Auth users change detected:', payload);
        // Check if this change affects the current user
        if (payload.new && typeof payload.new === 'object' && 'email' in payload.new && payload.new.email === user.value?.email) {
          console.log('🔄 Current user auth data changed, refreshing profiles...');
          await fetchElderlyProfiles();
        }
      }
    )
    
    // Listen to any other relevant table changes
    .on(
      'postgres_changes',
      {
        event: '*',
        schema: 'public',
        table: 'subscriptions'
      },
      async (payload) => {
        console.log('🔄 Subscriptions change detected:', payload);
        // Could trigger billing updates or feature access changes
      }
    )
    
    // Listen to database function calls or other events
    .on('broadcast', { event: 'database_sync' }, async (payload) => {
      console.log('🔄 Database sync broadcast received:', payload);
      await fetchElderlyProfiles();
    })
    
    .subscribe((status) => {
      console.log('🔄 Real-time subscription status:', status);
      if (status === 'SUBSCRIBED') {
        console.log('✅ Comprehensive real-time sync activated');
      } else if (status === 'CHANNEL_ERROR') {
        console.log('❌ Real-time sync error, attempting to reconnect...');
        setTimeout(() => setupRealtimeSubscription(), 5000);
      }
    });
};

// Periodic sync to ensure data consistency
const startPeriodicSync = () => {
  // Sync every 30 seconds to catch any missed real-time events
  periodicSyncInterval = setInterval(async () => {
    try {
      console.log('🔄 Periodic sync check...');
      await fetchElderlyProfiles();
    } catch (error) {
      console.warn('Periodic sync failed:', error);
    }
  }, 30000); // 30 seconds
  
  console.log('✅ Periodic sync started (every 30 seconds)');
};

const stopPeriodicSync = () => {
  if (periodicSyncInterval) {
    clearInterval(periodicSyncInterval);
    periodicSyncInterval = null;
    console.log('🛑 Periodic sync stopped');
  }
};

const fetchElderlyProfiles = async () => {
  try {
    loading.value = true;
    error.value = null;

    // Get current user's email from Supabase session
    const { data: { session } } = await supabase.auth.getSession();
    const currentUserEmail = session?.user?.email;
    
    console.log('Current user email:', currentUserEmail);
    
    if (!currentUserEmail) {
      throw new Error('User not authenticated');
    }

    console.log('Fetching profiles for:', currentUserEmail);

    // Fetch profiles directly from Supabase
    const { data, error: fetchError } = await supabase
      .from('elderly_profiles')
      .select('*')
      .eq('associated_account_email', currentUserEmail)
      .order('created_at', { ascending: false });

    // Also fetch user_questions from public.users table for the current user
    let userQuestions = '';
    try {
      const { data: userData, error: userError } = await supabase
        .from('users')
        .select('user_questions')
        .eq('email', currentUserEmail)
        .single();
      
      if (!userError && userData) {
        userQuestions = userData.user_questions || '';
        console.log('Fetched user_questions from public.users:', userQuestions);
      }
    } catch (userErr) {
      console.warn('Could not fetch user_questions from public.users:', userErr);
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
      lastActivity: profile.last_activity || 'Never',
      lastActivityType: profile.last_activity ? 'recent' : 'old',
      registered: profile.created_at ? new Date(profile.created_at).toLocaleDateString() : 'Unknown',
      role: profile.role || 'User',
      phone: profile.phone || '',
      status: profile.status || 'active',
      created_at: profile.created_at,
      updated_at: profile.updated_at,
      associated_account_email: profile.associated_account_email,
      user_questions: userQuestions // Use the fetched user_questions from public.users table
    }));

    console.log('Transformed profiles:', profiles.value);
    
  } catch (err: any) {
    console.error('Error fetching profiles:', err);
    error.value = err.message || 'Failed to fetch profiles';
  } finally {
    loading.value = false;
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

// Manual sync function for users to force refresh
const manualSync = async () => {
  try {
    console.log('🔄 Manual sync triggered by user');
    await fetchElderlyProfiles();
    console.log('✅ Manual sync completed');
  } catch (error) {
    console.error('Manual sync failed:', error);
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
      <h1 class="text-2xl font-bold mb-6 text-[#061B2B]">Elderly Profiles</h1>
      
      <!-- Sync Status Indicator -->
      <div class="flex items-center gap-2 mb-4 text-sm">
        <div class="flex items-center gap-2">
          <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
          <span class="text-[#2871B5] font-medium">Real-time sync active</span>
        </div>
        <span class="text-[#061B2B]/60">•</span>
        <span class="text-[#061B2B]/60">Auto-sync every 30s</span>
        <span class="text-[#061B2B]/60">•</span>
        <span class="text-[#061B2B]/60">Listening to: elderly_profiles, users, auth.users</span>
      </div>
      
      <!-- Compact Single Line Filters -->
      <div class="flex items-center gap-2 mb-6 w-full">
        <!-- Filters -->
        <select v-model="roleFilter" class="rounded-lg border border-[#061B2B]/20 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-[#2871B5] focus:border-[#2871B5] min-w-[90px] h-8 text-[#061B2B]">
          <option value="">Role</option>
          <option value="elderly user">Elderly User</option>
        </select>
        
        <select v-model="statusFilter" class="rounded-lg border border-[#061B2B]/20 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-[#2871B5] focus:border-[#2871B5] min-w-[85px] h-8 text-[#061B2B]">
          <option value="">Status</option>
          <option value="active">Active</option>
          <option value="deactivated">Deactivated</option>
        </select>
        
        <select v-model="registrationFilter" class="rounded-lg border border-[#061B2B]/20 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-[#2871B5] focus:border-[#2871B5] min-w-[110px] h-8 text-[#061B2B]">
          <option value="">Registration</option>
          <option value="recent">Recent</option>
          <option value="old">Old</option>
        </select>
        
        <select v-model="activityFilter" class="rounded-lg border border-[#061B2B]/20 px-2.5 py-1.5 text-xs bg-white focus:outline-none focus:ring-1 focus:ring-[#2871B5] focus:border-[#2871B5] min-w-[95px] h-8 text-[#061B2B]">
          <option value="">Activity</option>
          <option value="recent">Recent</option>
          <option value="old">Old</option>
        </select>
        
        <button @click="clearFilters" class="rounded-lg border border-[#061B2B]/20 px-2.5 py-1.5 text-xs flex items-center gap-1 bg-white hover:bg-[#061B2B]/5 hover:border-[#061B2B]/40 min-w-[70px] h-8 transition-colors text-[#061B2B]">
          <span>Clear</span>
          <span class="text-sm">&times;</span>
        </button>
        
        <!-- Spacer -->
        <div class="flex-1"></div>
        
        <!-- Search and Actions -->
        <input v-model="search" type="text" placeholder="Search profiles..." class="rounded-lg border border-[#061B2B]/20 px-3 py-1.5 text-xs w-48 focus:outline-none focus:ring-1 focus:ring-[#2871B5] focus:border-[#2871B5] h-8 text-[#061B2B]" />
        
        <!-- Sync Button -->
        <button 
          @click="manualSync" 
          class="rounded-lg border border-[#061B2B]/20 px-2 py-1.5 flex items-center justify-center bg-white hover:bg-[#2871B5]/5 hover:border-[#2871B5]/40 h-8 w-8 transition-colors text-[#2871B5]"
          title="Sync with Supabase"
        >
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
            <path d="M1 4v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M23 20v-6h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        
        <button class="rounded-lg border border-[#061B2B]/20 px-2 py-1.5 flex items-center justify-center bg-white hover:bg-[#061B2B]/5 hover:border-[#061B2B]/40 h-8 w-8 transition-colors text-[#061B2B]">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="2" rx="1" fill="currentColor"/><rect x="3" y="11" width="18" height="2" rx="1" fill="currentColor"/><rect x="3" y="17" width="18" height="2" rx="1" fill="currentColor"/></svg>
        </button>
        
        <a href="https://form.fillout.com/t/5WbxT5J6gxus" target="_blank" rel="noopener noreferrer" class="rounded-lg bg-[#2871B5] text-white px-4 py-1.5 flex items-center gap-1.5 text-xs font-medium hover:bg-[#061B2B] whitespace-nowrap h-8 transition-colors">
          Add user
          <span class="text-sm">+</span>
        </a>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2871B5] mx-auto mb-4"></div>
          <p class="text-[#061B2B]/70">Loading elderly profiles...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="flex items-center justify-center py-12">
        <div class="text-center">
          <p class="text-red-600 mb-4">{{ error }}</p>
          <button @click="fetchElderlyProfiles" class="px-4 py-2 bg-[#2871B5] text-white rounded-md hover:bg-[#061B2B]">
            Try Again
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredProfiles.length === 0" class="flex items-center justify-center py-12">
        <div class="text-center">
          <p class="text-[#061B2B]/70 mb-4">No elderly profiles found</p>
          <a href="https://form.fillout.com/t/5WbxT5J6gxus" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-[#2871B5] text-white rounded-md hover:bg-[#061B2B]">
            Add First Profile
          </a>
        </div>
      </div>

      <!-- Profiles Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div 
          v-for="profile in filteredProfiles" 
          :key="profile.id" 
          class="rounded-xl border border-neutral-200 bg-white shadow-sm p-6 relative cursor-pointer hover:shadow-md hover:border-[#2871B5]/30 transition-all duration-200"
          @click="openProfileDetail(profile.id)"
        >
          <!-- Status Badge -->
          <div class="absolute top-4 right-4">
            <span :class="[
              'px-3 py-1 rounded-full text-xs font-medium',
              profile.status === 'active' 
                ? 'bg-[#2871B5]/10 text-[#2871B5]' 
                : 'bg-red-100 text-red-800'
            ]">
              {{ profile.status === 'active' ? 'Active' : 'Deactivated' }}
            </span>
          </div>
          
          <div class="flex items-center gap-4 mb-4">
            <img :src="profile.avatar" alt="avatar" class="w-14 h-14 rounded-full object-cover border border-black" />
            <div class="flex-1">
              <div class="font-semibold text-lg text-[#061B2B]">{{ profile.name }}</div>
              <div class="text-[#061B2B]/60 text-sm">{{ profile.email }}</div>
            </div>
            <div class="relative dropdown-container">
              <button 
                @click.stop="toggleDropdown(profile.id, $event)"
                class="text-[#061B2B] hover:text-white hover:bg-gradient-to-r hover:from-[#2871B5] hover:to-[#061B2B] p-2 rounded-full transition-colors"
              >
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="19" r="1.5" fill="currentColor"/></svg>
              </button>
              <!-- Dropdown menu for actions -->
              <div 
                v-if="openDropdown === profile.id"
                class="absolute right-0 top-full mt-1 bg-white border border-[#061B2B]/20 rounded-lg shadow-lg py-1 z-10 min-w-[120px]"
              >
                <button 
                  @click.stop="openEditForm(profile)"
                  class="w-full text-left px-4 py-2 text-sm text-[#2871B5] hover:bg-[#2871B5]/10"
                >
                  Edit Profile
                </button>
                
                <button 
                  @click.stop="openPersonalisationForm(profile)"
                  class="w-full text-left px-4 py-2 text-sm text-[#2871B5] hover:bg-[#2871B5]/10"
                >
                  Profile Personalisation
                </button>
                
                <hr class="my-1 border-[#061B2B]/20" />
                
                <button 
                  v-if="profile.status === 'active'"
                  @click.stop="updateProfileStatus(profile, 'inactive')"
                  class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-orange-50"
                >
                  Deactivate
                </button>
                
                <button 
                  v-else
                  @click.stop="updateProfileStatus(profile, 'active')"
                  class="w-full text-left px-4 py-2 text-sm text-[#2871B5] hover:bg-[#2871B5]/10"
                >
                  Activate
                </button>
              </div>
            </div>
          </div>
          <hr class="my-4 border-black/10" />
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="text-[#061B2B]/50">Last Activity</div>
              <div class="flex items-center gap-1">
                <span :class="['h-2 w-2 rounded-full mr-1', profile.lastActivityType === 'recent' ? 'bg-[#2871B5]' : 'bg-[#061B2B]/30']"></span>
                <span :class="profile.lastActivityType === 'recent' ? 'text-[#061B2B]' : 'text-[#061B2B]/50'">{{ profile.lastActivity }}</span>
              </div>
            </div>
            <div>
              <div class="text-[#061B2B]/50">Registered</div>
              <div class="text-[#061B2B]">{{ profile.registered }}</div>
            </div>
            <div>
              <span class="px-4 py-1 rounded-full text-xs font-medium bg-[#061B2B]/10 text-[#061B2B]">
                {{ profile.role }}
              </span>
            </div>
          </div>
          
          <!-- Click indicator -->
          <div class="mt-4 pt-4 border-t border-neutral-200 flex items-center justify-between">
            <span class="text-xs text-[#2871B5] font-medium">Click to view details</span>
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="text-[#2871B5]">
              <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Profile Modal -->
    <div v-if="showAddModal" class="fixed inset-0 bg-[#061B2B]/50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-[#061B2B] mb-4">Add New Profile</h3>
          
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter name"
              />
              <span v-if="formErrors.name" class="text-red-500 text-xs">{{ formErrors.name }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter email"
                required
              />
              <span v-if="formErrors.email" class="text-red-500 text-xs">{{ formErrors.email }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input
                v-model="form.phone"
                type="tel"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter phone number"
              />
              <span v-if="formErrors.phone" class="text-red-500 text-xs">{{ formErrors.phone }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
              <span v-if="formErrors.status" class="text-red-500 text-xs">{{ formErrors.status }}</span>
            </div>
            
            <div v-if="formErrors.general" class="text-red-500 text-sm">{{ formErrors.general }}</div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-[#061B2B] bg-[#061B2B]/10 rounded-lg hover:bg-[#061B2B]/20 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="px-4 py-2 bg-[#2871B5] text-white rounded-lg hover:bg-[#061B2B] disabled:opacity-50 transition-colors"
              >
                <span v-if="isSaving" class="flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Saving...
                </span>
                <span v-else>Add Profile</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-[#061B2B]/50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-[#061B2B] mb-4">Edit Profile</h3>
          
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter name"
              />
              <span v-if="formErrors.name" class="text-red-500 text-xs">{{ formErrors.name }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter email"
                required
              />
              <span v-if="formErrors.email" class="text-red-500 text-xs">{{ formErrors.email }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input
                v-model="form.phone"
                type="tel"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter phone number"
              />
              <span v-if="formErrors.phone" class="text-red-500 text-xs">{{ formErrors.phone }}</span>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="form.status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
              <span v-if="formErrors.status" class="text-red-500 text-xs">{{ formErrors.status }}</span>
            </div>
            
            <div v-if="formErrors.general" class="text-red-500 text-sm">{{ formErrors.general }}</div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-[#061B2B] bg-[#061B2B]/10 rounded-lg hover:bg-[#061B2B]/20 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="px-4 py-2 bg-[#2871B5] text-white rounded-lg hover:bg-[#061B2B] disabled:opacity-50 transition-colors"
              >
                <span v-if="isSaving" class="flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Saving...
                </span>
                <span v-else>Save Changes</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Profile Personalisation Modal -->
    <div v-if="showPersonalisationModal" class="fixed inset-0 bg-[#061B2B]/50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-[500px] shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-[#061B2B] mb-4">Profile Personalisation</h3>
          <p class="text-sm text-gray-600 mb-4">Customize the story and personal details for {{ currentProfile?.name || 'this profile' }}</p>
          
          <form @submit.prevent="savePersonalisation" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Personal Story & Questions</label>
              <textarea
                v-model="personalisationForm.user_questions"
                rows="6"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                placeholder="Share the person's story, background, preferences, or any specific questions you'd like to ask them..."
              ></textarea>
              <p class="text-xs text-gray-500 mt-1">This information helps personalize interactions and care for the elderly person.</p>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-[#061B2B] bg-[#061B2B]/10 rounded-lg hover:bg-[#061B2B]/20 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isPersonalising"
                class="px-4 py-2 bg-[#2871B5] text-white rounded-lg hover:bg-[#061B2B] disabled:opacity-50 transition-colors"
              >
                <span v-if="isPersonalising" class="flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Saving...
                </span>
                <span v-else>Save Personalisation</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 