<template>
  <AdminLayout title="Elderly Profiles Management" subtitle="Manage elderly user profiles and their status">
    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Search -->
        <div class="flex-1 max-w-md">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="searchForm.search"
              type="text"
              placeholder="Search profiles..."
              class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="search"
            />
          </div>
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-4">
          <select
            v-model="searchForm.status"
            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            @change="search"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
          </select>

          <button
            @click="clearFilters"
            class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-colors"
          >
            Clear
          </button>

          <!-- Sync Button -->
          <button
            @click="syncFromSupabase"
            :disabled="isSyncing"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            <span v-if="isSyncing" class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Syncing...
            </span>
            <span v-else class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              Sync from Supabase
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- Profiles Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Profile
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Associated Account
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Phone
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Created
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="profile in profiles.data" :key="profile.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ profile.name || 'No Name' }}</div>
                    <div class="text-sm text-gray-500">{{ profile.email }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ profile.associated_account_email }}</div>
                <div v-if="profile.user" class="text-sm text-gray-500">{{ profile.user.name }}</div>
                <div v-else class="text-sm text-red-500">No associated user</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ profile.phone || 'N/A' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                    getStatusColor(profile.status)
                  ]"
                >
                  {{ profile.status || 'active' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(profile.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                  <select
                    :value="profile.status || 'active'"
                    @change="updateProfileStatus(profile, $event)"
                    class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="pending">Pending</option>
                  </select>
                  
                  <button
                    @click="editProfile(profile)"
                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50 transition-colors"
                    title="Edit Profile"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  
                  <button
                    @click="deleteProfile(profile)"
                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors"
                    title="Delete Profile"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="profiles.data.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No profiles found</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ searchForm.search || searchForm.status ? 'Try adjusting your search or filters.' : 'No elderly profiles have been created yet.' }}
        </p>
      </div>

      <!-- Pagination -->
      <div v-if="profiles.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link
              v-if="profiles.links[0].url"
              :href="profiles.links[0].url || '#'"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </Link>
            <Link
              v-if="profiles.links[profiles.links.length - 1].url"
              :href="profiles.links[profiles.links.length - 1].url || '#'"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">{{ profiles.from }}</span>
                to
                <span class="font-medium">{{ profiles.to }}</span>
                of
                <span class="font-medium">{{ profiles.total }}</span>
                results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <Link
                  v-for="(link, index) in profiles.links"
                  :key="index"
                  :href="link.url || '#'"
                  :class="[
                    'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                    link.active
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : link.url
                      ? 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                      : 'bg-white border-gray-300 text-gray-400 cursor-not-allowed',
                    index === 0 ? 'rounded-l-md' : '',
                    index === profiles.links.length - 1 ? 'rounded-r-md' : ''
                  ]"
                  v-html="link.label"
                />
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Profile Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Profile</h3>
          
          <form @submit.prevent="saveProfile" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                v-model="editForm.name"
                type="text"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter name"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                v-model="editForm.email"
                type="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter email"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
              <input
                v-model="editForm.phone"
                type="tel"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter phone number"
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                v-model="editForm.status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Associated Account Email</label>
              <input
                v-model="editForm.associated_account_email"
                type="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Enter associated account email"
              />
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeEditModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
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
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'

interface User {
  id: number
  name: string
  email: string
}

interface ElderlyProfile {
  id: number
  name: string | null
  email: string
  phone: string | null
  status: string | null
  associated_account_email: string
  created_at: string
  user?: User
}

interface PaginatedProfiles {
  data: ElderlyProfile[]
  links: Array<{ label: string; url: string | null; active: boolean }>
  from: number
  to: number
  total: number
}

interface Props {
  profiles: PaginatedProfiles
  filters: {
    search?: string
    status?: string
  }
}

const props = defineProps<Props>()

const searchForm = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
})

const search = () => {
  router.get(route('admin.elderly-profiles'), searchForm, {
    preserveState: true,
    replace: true,
  })
}

const clearFilters = () => {
  searchForm.search = ''
  searchForm.status = ''
  search()
}

const updateProfileStatus = (profile: ElderlyProfile, event: Event) => {
  const status = (event.target as HTMLSelectElement).value
  
  if (confirm(`Are you sure you want to change the status of ${profile.name || profile.email} to ${status}?`)) {
    router.patch(route('admin.elderly-profiles.update-status', profile.id), {
      status: status,
    }, {
      preserveScroll: true,
    })
  }
}

const getStatusColor = (status: string | null) => {
  switch (status) {
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'inactive':
      return 'bg-red-100 text-red-800'
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    default:
      return 'bg-green-100 text-green-800' // Default to active
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

// Edit modal state
const showEditModal = ref(false)
const isSaving = ref(false)
const isSyncing = ref(false)
const currentProfile = ref<ElderlyProfile | null>(null)

const editForm = reactive({
  name: '',
  email: '',
  phone: '',
  status: 'active',
  associated_account_email: '',
})

// Edit profile
const editProfile = (profile: ElderlyProfile) => {
  currentProfile.value = profile
  editForm.name = profile.name || ''
  editForm.email = profile.email
  editForm.phone = profile.phone || ''
  editForm.status = profile.status || 'active'
  editForm.associated_account_email = profile.associated_account_email || ''
  showEditModal.value = true
}

// Close edit modal
const closeEditModal = () => {
  showEditModal.value = false
  currentProfile.value = null
  editForm.name = ''
  editForm.email = ''
  editForm.phone = ''
  editForm.status = 'active'
  editForm.associated_account_email = ''
}

// Save profile changes
const saveProfile = () => {
  if (!currentProfile.value) return
  
  isSaving.value = true
  
  router.patch(route('admin.elderly-profiles.update', currentProfile.value.id), editForm, {
    preserveScroll: true,
    onSuccess: () => {
      closeEditModal()
      isSaving.value = false
    },
    onError: () => {
      isSaving.value = false
    },
  })
}

// Delete profile
const deleteProfile = (profile: ElderlyProfile) => {
  if (confirm(`Are you sure you want to delete the profile for ${profile.name || profile.email}?`)) {
    router.delete(route('admin.elderly-profiles.delete', profile.id), {
      preserveScroll: true,
    })
  }
}

// Sync from Supabase
const syncFromSupabase = async () => {
  isSyncing.value = true
  
  try {
    const response = await fetch('/admin/sync-elderly-profiles', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
    })
    
    if (response.ok) {
      // Reload the page to show updated data
      router.reload()
    } else {
      console.error('Failed to sync profiles')
    }
  } catch (error) {
    console.error('Error syncing profiles:', error)
  } finally {
    isSyncing.value = false
  }
}
</script> 