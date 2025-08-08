<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface User {
  id: number;
  name: string;
  email: string;
  user_address?: string;
  user_phone_number?: string;
  user_pronouns?: string;
}

interface Props {
  user?: User;
  mustVerifyEmail: boolean;
  status?: string;
}

const props = defineProps<Props>();
const page = usePage();

const form = ref({
  name: props.user?.name || '',
  email: props.user?.email || '',
  user_address: props.user?.user_address || '',
  user_phone_number: props.user?.user_phone_number || '',
  user_pronouns: props.user?.user_pronouns || '',
});

const isSaving = ref(false);
const showSuccess = ref(false);

const submit = () => {
  if (!props.user) {
    console.error('User data not available');
    return;
  }
  
  isSaving.value = true;
  showSuccess.value = false;
  
  router.patch(route('profile.update'), form.value, {
    preserveScroll: true,
    onSuccess: () => {
      isSaving.value = false;
      showSuccess.value = true;
      // Hide success message after 3 seconds
      setTimeout(() => {
        showSuccess.value = false;
      }, 3000);
    },
    onError: () => {
      isSaving.value = false;
    },
  });
};
</script>

<template>
  <AppLayout>
    <SettingsLayout>
      <!-- Toast notification -->
      <div v-if="showSuccess" class="fixed top-4 right-4 z-50">
        <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-in slide-in-from-right-2 duration-300">
          <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
          </svg>
          <span class="font-medium">Profile saved successfully!</span>
        </div>
      </div>
      
      <div class="flex flex-col space-y-6 max-w-lg mx-auto mt-8">
        <HeadingSmall title="Profile information" description="Update your personal information" />
        
        <div v-if="!props.user" class="text-center py-8">
          <p class="text-gray-500">Loading user data...</p>
          <p class="text-sm text-gray-400 mt-2">Please ensure you are logged in.</p>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
          </div>
          
          <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" placeholder="Email address" />
          </div>

          <div class="grid gap-2">
            <Label for="user_address">Address</Label>
            <textarea 
              id="user_address" 
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" 
              v-model="form.user_address" 
              placeholder="Enter your address"
              rows="3"
            />
          </div>

          <div class="grid gap-2">
            <Label for="user_phone_number">Phone Number</Label>
            <Input 
              id="user_phone_number" 
              type="tel" 
              class="mt-1 block w-full" 
              v-model="form.user_phone_number" 
              autocomplete="tel" 
              placeholder="Enter your phone number" 
            />
          </div>

          <div class="grid gap-2">
            <Label for="user_pronouns">Pronouns</Label>
            <Input 
              id="user_pronouns" 
              class="mt-1 block w-full" 
              v-model="form.user_pronouns" 
              placeholder="e.g., he/him, she/her, they/them" 
            />
          </div>

          <InputError v-if="page.props.errors?.name" :message="page.props.errors.name" />
          <InputError v-if="page.props.errors?.email" :message="page.props.errors.email" />
          <InputError v-if="page.props.errors?.user_address" :message="page.props.errors.user_address" />
          <InputError v-if="page.props.errors?.user_phone_number" :message="page.props.errors.user_phone_number" />
          <InputError v-if="page.props.errors?.user_pronouns" :message="page.props.errors.user_pronouns" />

          <div class="flex items-center gap-4">
            <Button type="submit" :disabled="isSaving">
              <span v-if="isSaving" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Saving...
              </span>
              <span v-else>Save</span>
            </Button>
            

            
            <span v-if="(page.props.flash as any)?.status" class="text-green-600">{{ (page.props.flash as any).status }}</span>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
