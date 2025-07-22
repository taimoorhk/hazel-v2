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
import { supabase } from '@/lib';
import { useSupabaseUser } from '@/composables/useSupabaseUser';

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const errorMsg = ref('');
const loading = ref(false);
const successMsg = ref('');

const { user } = useSupabaseUser();

onMounted(async () => {
    const { data } = await supabase.auth.getSession();
    if (data.session) {
        window.location.href = '/dashboard';
    }
});

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    if (!form.value.name || !form.value.email || !form.value.password || !form.value.password_confirmation) {
        errorMsg.value = 'All fields are required.';
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
            data: { name: form.value.name },
        },
    });
    loading.value = false;
    if (error) {
        errorMsg.value = error.message;
    } else if (data && data.user && !data.session) {
        // User created, but email verification required
        successMsg.value = 'Check your email for a verification link.';
    } else if (data && data.user) {
        window.location.href = '/dashboard';
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
    </AuthBase>
</template>
