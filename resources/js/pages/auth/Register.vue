<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderCircle, Mail, CheckCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import { useSupabaseUser } from '@/composables/useSupabaseUser';

const form = ref({
    name: '',
    email: '',
    role: '',
});
const errorMsg = ref('');
const loading = ref(false);
const successMsg = ref('');
const emailSent = ref(false);

const { user } = useSupabaseUser();

onMounted(async () => {
    const { data, error } = await supabase.auth.getSession();
    if (data.session) {
        window.location.href = '/dashboard';
    }
});

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    
    try {
        if (!form.value.name || !form.value.email || !form.value.role) {
            errorMsg.value = 'All fields are required, including role.';
            loading.value = false;
            return;
        }
        
        // First, check if user already exists in either system
        const verifyResponse = await fetch('/api/verify-user-exists', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: form.value.email
            })
        });
        
        const verifyResult = await verifyResponse.json();
        
        if (verifyResponse.ok && verifyResult.exists) {
            errorMsg.value = 'User already exists. Please use the login page instead.';
            loading.value = false;
            return;
        }
        
        // User doesn't exist, proceed with registration
        const { data, error } = await supabase.auth.signInWithOtp({
            email: form.value.email,
            options: {
                data: {
                    name: form.value.name,
                    display_name: form.value.name,
                    role: form.value.role,
                },
                emailRedirectTo: `http://24.199.87.186/auth/confirm`,
                shouldCreateUser: true,
            },
        });
        
        loading.value = false;
        
        if (error) {
            errorMsg.value = error.message;
        } else {
            // Magic link sent successfully
            emailSent.value = true;
            successMsg.value = `Magic link sent to ${form.value.email}. Please check your email and click the link to complete your registration.`;
        }
    } catch (e) {
        errorMsg.value = 'Network error: Unable to reach authentication server.';
        loading.value = false;
    }
};

const resendMagicLink = async () => {
    await submit();
};
</script>

<template>
    <AuthBase>
        <Head title="Register" />
        
        <!-- Email Sent Success State -->
        <div v-if="emailSent" class="text-center space-y-6">
            <div class="flex justify-center">
                <CheckCircle class="h-16 w-16 text-green-500" />
            </div>
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-gray-900">Check your email</h2>
                <p class="text-gray-600">{{ successMsg }}</p>
                <div class="p-4 rounded-lg" style="background-color: #f9fafb;">
                    <p class="text-sm text-gray-500">
                        Didn't receive the email? Check your spam folder or
                        <button 
                            @click="resendMagicLink" 
                            :disabled="loading"
                            class="text-primary hover:underline font-medium"
                        >
                            click here to resend
                        </button>
                    </p>
                </div>
            </div>
            <Button @click="emailSent = false" variant="outline" class="w-full">
                Try a different email
            </Button>
        </div>

        <!-- Registration Form -->
        <div v-else>
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create your account</h1>
                <p class="text-gray-600 mt-2">Enter your details to receive a magic link</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <Label for="name">Full Name</Label>
                    <Input 
                        id="name" 
                        v-model="form.name" 
                        type="text" 
                        required 
                        autofocus 
                        placeholder="Enter your full name"
                    />
                </div>
                
                <div>
                    <Label for="email">Email address</Label>
                    <Input 
                        id="email" 
                        v-model="form.email" 
                        type="email" 
                        required 
                        placeholder="Enter your email"
                    />
                </div>
                
                <div>
                    <Label for="role">Role</Label>
                    <select 
                        id="role" 
                        v-model="form.role" 
                        required 
                        class="w-full rounded border border-gray-300 px-3 py-2"
                    >
                        <option value="" disabled>Select a role</option>
                        <option value="Normal User">Normal User</option>
                        <option value="Caregiver">Caregiver</option>
                        <option value="Organization">Organization</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                
                <InputError :message="errorMsg" />
                
                <Button :disabled="loading" class="w-full" type="submit">
                    <Mail v-if="!loading" class="h-4 w-4 mr-2" />
                    <LoaderCircle v-else class="animate-spin h-4 w-4 mr-2" />
                    {{ loading ? 'Sending magic link...' : 'Send magic link' }}
                </Button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Already have an account? 
                    <TextLink href="/login">Sign in here</TextLink>
                </p>
            </div>
        </div>
    </AuthBase>
</template>
