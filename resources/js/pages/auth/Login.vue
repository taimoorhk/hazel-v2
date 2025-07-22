<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib';

const form = ref({
    email: '',
    password: '',
    remember: false,
});
const errorMsg = ref('');
const loading = ref(false);

onMounted(async () => {
    const { data } = await supabase.auth.getSession();
    if (data.session && data.session.user) {
        window.location.href = '/dashboard';
    }
});

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    if (!form.value.email || !form.value.password) {
        errorMsg.value = 'Email and password are required.';
        loading.value = false;
        return;
    }
    const { data, error } = await supabase.auth.signInWithPassword({
        email: form.value.email,
        password: form.value.password,
    });
    loading.value = false;
    if (error) {
        errorMsg.value = error.message;
    } else if (data && data.user) {
        if (data.user.email_confirmed_at) {
            window.location.href = '/dashboard';
        } else {
            errorMsg.value = 'Please verify your email before logging in.';
        }
    }
};
</script>

<template>
    <AuthBase>
        <Head title="Login" />
        <div v-if="loading" class="flex items-center justify-center h-96">
            <LoaderCircle class="animate-spin h-8 w-8 text-primary" />
        </div>
        <form v-else @submit.prevent="submit" class="space-y-6">
            <div>
                <Label for="email">Email</Label>
                <Input id="email" v-model="form.email" type="email" required autofocus />
            </div>
            <div>
                <Label for="password">Password</Label>
                <Input id="password" v-model="form.password" type="password" required />
            </div>
            <InputError :message="errorMsg" />
            <Button :disabled="loading" class="w-full">
                Login
            </Button>
        </form>
        <div class="mt-4 text-center">
            <TextLink href="/register">Don't have an account? Register</TextLink>
        </div>
    </AuthBase>
</template>
