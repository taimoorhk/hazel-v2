<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ref } from 'vue';

useAuthGuard();
const { user } = useSupabaseUser();

const search = ref('');
const roleFilter = ref('');
const registrationFilter = ref('');
const activityFilter = ref('');

const profiles = [
  {
    name: 'James Carter',
    email: 'james.carter@company.com',
    avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
    lastActivity: 'Recently',
    lastActivityType: 'recent',
    registered: '25 Apr 2025',
    role: 'Admin',
  },
  {
    name: 'Emily Johnson',
    email: 'emily.johnson@company.com',
    avatar: 'https://randomuser.me/api/portraits/women/44.jpg',
    lastActivity: 'Long ago',
    lastActivityType: 'old',
    registered: '31 Dec 2025',
    role: 'User',
  },
  {
    name: 'Michael Lee',
    email: 'michael.lee@company.com',
    avatar: 'https://randomuser.me/api/portraits/men/45.jpg',
    lastActivity: 'Recently',
    lastActivityType: 'recent',
    registered: '10 Jan 2025',
    role: 'User',
  },
  {
    name: 'Sarah Kim',
    email: 'sarah.kim@company.com',
    avatar: 'https://randomuser.me/api/portraits/women/65.jpg',
    lastActivity: 'Long ago',
    lastActivityType: 'old',
    registered: '05 Feb 2025',
    role: 'Admin',
  },
  {
    name: 'David Brown',
    email: 'david.brown@company.com',
    avatar: 'https://randomuser.me/api/portraits/men/77.jpg',
    lastActivity: 'Recently',
    lastActivityType: 'recent',
    registered: '20 Mar 2025',
    role: 'User',
  },
  {
    name: 'Olivia Smith',
    email: 'olivia.smith@company.com',
    avatar: 'https://randomuser.me/api/portraits/women/88.jpg',
    lastActivity: 'Long ago',
    lastActivityType: 'old',
    registered: '15 Nov 2025',
    role: 'User',
  },
];

const clearFilters = () => {
  search.value = '';
  roleFilter.value = '';
  registrationFilter.value = '';
  activityFilter.value = '';
};
</script>

<template>
  <AppLayout>
    <Head title="Elderly Profiles" />
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-8">Elderly Profiles</h1>
      <div class="flex flex-wrap items-center gap-3 mb-8 w-full">
        <!-- Filters Left -->
        <select v-model="roleFilter" class="rounded-full border px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-200 min-w-[110px] h-10">
          <option value="">User role</option>
          <option value="Admin">Admin</option>
          <option value="User">User</option>
        </select>
        <select v-model="registrationFilter" class="rounded-full border px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-200 min-w-[140px] h-10">
          <option value="">Date of registration</option>
          <option value="recent">Recently Registered</option>
          <option value="old">Registered Long Ago</option>
        </select>
        <select v-model="activityFilter" class="rounded-full border px-3 py-1.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-primary-200 min-w-[120px] h-10">
          <option value="">Last activity</option>
          <option value="recent">Recently Active</option>
          <option value="old">Inactive</option>
        </select>
        <button @click="clearFilters" class="rounded-full border px-4 py-1.5 text-sm flex items-center gap-1 bg-white hover:bg-neutral-100 min-w-[100px] h-10">
          <span>Clear All</span>
          <span class="text-lg">&times;</span>
        </button>
        <!-- Spacer -->
        <div class="flex-1"></div>
        <!-- Search and Actions Right -->
        <input v-model="search" type="text" placeholder="Search by any parameter" class="rounded-full border px-4 py-1.5 text-sm w-full md:w-72 focus:outline-none focus:ring-2 focus:ring-primary-200 h-10" />
        <button class="rounded-full border px-2 py-1.5 flex items-center justify-center bg-white hover:bg-neutral-100 h-10 w-10">
          <svg width="22" height="22" fill="none" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="2" rx="1" fill="#1e293b"/><rect x="3" y="11" width="18" height="2" rx="1" fill="#1e293b"/><rect x="3" y="17" width="18" height="2" rx="1" fill="#1e293b"/></svg>
        </button>
        <button class="rounded-full bg-cyan-800 text-white px-6 py-1.5 flex items-center gap-2 font-semibold hover:bg-cyan-900 whitespace-nowrap h-10">
          Add user
          <span class="text-lg">+</span>
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div v-for="profile in profiles" :key="profile.email" :class="['rounded-xl border p-6 relative bg-white', profile.role === 'Admin' ? 'border-blue-200 bg-blue-50' : 'border-neutral-200']">
          <div class="flex items-center gap-4 mb-4">
            <img :src="profile.avatar" alt="avatar" class="w-14 h-14 rounded-full object-cover border border-neutral-200" />
            <div class="flex-1">
              <div class="font-semibold text-lg">{{ profile.name }}</div>
              <div class="text-neutral-500 text-sm">{{ profile.email }}</div>
            </div>
            <button class="text-neutral-400 hover:text-neutral-600 p-2 rounded-full transition-colors">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="19" r="1.5" fill="currentColor"/></svg>
            </button>
          </div>
          <hr class="my-4 border-neutral-100" />
          <div class="flex items-center justify-between text-sm">
            <div>
              <div class="text-neutral-400">Last Activity</div>
              <div class="flex items-center gap-1">
                <span :class="['h-2 w-2 rounded-full mr-1', profile.lastActivityType === 'recent' ? 'bg-green-500' : 'bg-red-400']"></span>
                <span :class="profile.lastActivityType === 'recent' ? 'text-green-700' : 'text-red-500'">{{ profile.lastActivity }}</span>
              </div>
            </div>
            <div>
              <div class="text-neutral-400">Registered</div>
              <div>{{ profile.registered }}</div>
            </div>
            <div>
              <span :class="['px-4 py-1 rounded-full text-xs font-medium', profile.role === 'Admin' ? 'bg-purple-100 text-purple-700' : 'bg-neutral-100 text-neutral-500']">
                {{ profile.role }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 