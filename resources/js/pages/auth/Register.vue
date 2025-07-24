<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import { useSupabaseUser } from '@/composables/useSupabaseUser';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});
const errorMsg = ref('');
const loading = ref(false);
const successMsg = ref('');
const debugInfo = ref('');

const { user } = useSupabaseUser();

onMounted(async () => {
    const { data, error } = await supabase.auth.getSession();
    debugInfo.value = `Session: ${JSON.stringify(data.session)}, Error: ${error}`;
    if (data.session) {
        window.location.href = '/dashboard';
    }
});

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    try {
        if (!form.value.name || !form.value.email || !form.value.password || !form.value.password_confirmation || !form.value.role) {
            errorMsg.value = 'All fields are required, including role.';
            loading.value = false;
            return;
        }
        if (form.value.password !== form.value.password_confirmation) {
            errorMsg.value = 'Passwords do not match.';
            loading.value = false;
            return;
        }
        const { data, error } = await supabase.auth.signUp({
            email: form.value.email,
            password: form.value.password,
            options: {
                data: {
                  name: form.value.name,
                  display_name: form.value.name,
                  role: form.value.role,
                },
            },
        });
        loading.value = false;
        if (error) {
            errorMsg.value = error.message;
        } else if (data && data.user && !data.session) {
            // Sync with backend
            await fetch('/api/sync-supabase-user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: data.user.id,
                    email: data.user.email,
                    name: data.user.user_metadata?.name || '',
                    role: data.user.user_metadata?.role || 'Normal User',
                })
            });
            // User created, but email verification required
            successMsg.value = 'Check your email for a verification link.';
        } else if (data && data.user) {
            // Sync with backend
            await fetch('/api/sync-supabase-user', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: data.user.id,
                    email: data.user.email,
                    name: data.user.user_metadata?.name || '',
                    role: data.user.user_metadata?.role || 'Normal User',
                })
            });
            window.location.href = '/dashboard';
        }
    } catch (e) {
        errorMsg.value = 'Network error: Unable to reach authentication server.';
        loading.value = false;
    }
};
</script>

<template>
    <AuthBase>
        <Head title="Register" />
        <div v-if="loading" class="flex items-center justify-center h-96">
            <LoaderCircle class="animate-spin h-8 w-8 text-primary" />
        </div>
        <form v-else @submit.prevent="submit" class="space-y-6">
            <div>
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" type="text" required autofocus />
            </div>
            <div>
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required />
            </div>
            <div>
                <Label for="role">Role</Label>
                <select id="role" v-model="form.role" required class="w-full rounded border border-gray-300 px-3 py-2">
                    <option value="" disabled>Select a role</option>
                    <option value="Normal User">Normal User</option>
                    <option value="Caregiver">Caregiver</option>
                    <option value="Organization">Organization</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div>
                <Label for="password">Password</Label>
                <Input id="password" v-model="form.password" type="password" required />
            </div>
            <div>
                <Label for="password_confirmation">Confirm Password</Label>
                <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required />
            </div>
            <InputError :message="errorMsg" />
            <Button :disabled="loading" class="w-full">
                Register
            </Button>
        </form>
        <div class="mt-4 text-center">
            <TextLink href="/login">Already have an account? Login</TextLink>
        </div>
        <div v-if="successMsg" class="mt-4 text-green-600 text-center">{{ successMsg }}</div>
        <div class="mt-4 text-xs text-gray-400">Debug: {{ debugInfo }}</div>
    </AuthBase>
</template>
