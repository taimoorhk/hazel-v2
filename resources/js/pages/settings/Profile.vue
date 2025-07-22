<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface SupabaseUser {
  id: string;
  email: string;
  user_metadata?: { name?: string; display_name?: string };
}

const user = ref<SupabaseUser | null>(null);
const form = ref<{ name: string; email: string }>({ name: '', email: '' });
const errorMsg = ref('');
const successMsg = ref('');

const fetchUser = async () => {
  const { data } = await supabase.auth.getUser();
  user.value = data.user as SupabaseUser;
  form.value.name = data.user?.user_metadata?.display_name || data.user?.user_metadata?.name || '';
  form.value.email = data.user?.email || '';
};

onMounted(fetchUser);

const submit = async () => {
  errorMsg.value = '';
  successMsg.value = '';
  const updates: any = {};
  // Always update display_name in user_metadata
  if (form.value.name !== user.value?.user_metadata?.display_name) {
    updates.data = { display_name: form.value.name };
  }
  if (form.value.email !== user.value?.email) {
    updates.email = form.value.email;
  }
  if (Object.keys(updates).length === 0) {
    successMsg.value = 'No changes to update.';
    return;
  }
  const { error } = await supabase.auth.updateUser(updates);
  if (error) {
    errorMsg.value = error.message;
  } else {
    successMsg.value = 'Profile updated successfully!';
    await fetchUser(); // Refresh user data after update
  }
};
</script>

<template>
  <AppLayout>
    <SettingsLayout>
      <div class="flex flex-col space-y-6 max-w-lg mx-auto mt-8">
        <HeadingSmall title="Profile information" description="Update your name and email address" />
        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid gap-2">
            <Label for="name">Name</Label>
            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Full name" />
          </div>
          <div class="grid gap-2">
            <Label for="email">Email address</Label>
            <Input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" placeholder="Email address" />
          </div>
          <InputError v-if="errorMsg" :message="errorMsg" />
          <div class="flex items-center gap-4">
            <Button type="submit">Save</Button>
            <span v-if="successMsg" class="text-green-600">{{ successMsg }}</span>
          </div>
        </form>
      </div>
    </SettingsLayout>
  </AppLayout>
</template>
