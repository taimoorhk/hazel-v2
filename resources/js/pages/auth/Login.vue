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
import { usePostHog } from '@/composables/usePostHog';

const form = ref({
    email: '',
});
const errorMsg = ref('');
const successMsg = ref('');
const loading = ref(false);
const emailSent = ref(false);

const submit = async () => {
    loading.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    
    try {
        if (!form.value.email) {
            errorMsg.value = 'Email is required.';
            loading.value = false;
            return;
        }
        
        // First, verify that the user exists in both Laravel and Supabase
        const verifyResponse = await fetch('/api/verify-user-exists', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                email: form.value.email
            })
        });
        
        const verifyResult = await verifyResponse.json();
        
        if (!verifyResponse.ok) {
            errorMsg.value = 'Verification failed. Please try again.';
            loading.value = false;
            return;
        }
        
        if (!verifyResult.exists) {
            errorMsg.value = verifyResult.message || 'User not found. Please register first.';
            loading.value = false;
            return;
        }
        
        // User exists, now send Magic Link
        // If user needs Supabase sync, allow user creation
        const shouldCreateUser = verifyResult.needs_supabase_sync || false;
        
        const { data, error } = await supabase.auth.signInWithOtp({
            email: form.value.email,
            options: {
                emailRedirectTo: `http://24.199.87.186/auth/confirm`,
                shouldCreateUser: shouldCreateUser, // Allow creation for users who need sync
            },
        });
        
        loading.value = false;
        
        if (error) {
            errorMsg.value = error.message;
        } else {
            // Magic link sent successfully
            emailSent.value = true;
            successMsg.value = `Magic link sent to ${form.value.email}. Please check your email and click the link to sign in.`;
        }
    } catch (e) {
        errorMsg.value = 'Network error: Unable to reach authentication server.';
        loading.value = false;
    }
};

const resendMagicLink = async () => {
    await submit();
};

const { user, session } = useSupabaseUser();
const { posthog } = usePostHog();

onMounted(() => {
  // Track login page view
  posthog.capture('login_page_viewed', {
    user_email: user.value?.email,
    user_id: user.value?.id,
  });
  
  if (session && session.value) {
    window.location.href = '/dashboard';
  }
});

function handleSignUpClick() {
  posthog.capture('sign_up_link_clicked', { 
    user_email: user.value?.email, 
    user_id: user.value?.id, 
    form_url: 'https://form.fillout.com/t/bVrkgZUwVEus' 
  });
}
</script>

<template>
    <AuthBase>
        <Head title="Login" />
        
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

        <!-- Login Form -->
        <div v-else>
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Sign in to your account</h1>
                <p class="text-gray-600 mt-2">Enter your email to receive a magic link</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <Label for="email">Email address</Label>
                    <Input 
                        id="email" 
                        v-model="form.email" 
                        type="email" 
                        required 
                        autofocus 
                        placeholder="Enter your email"
                    />
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
                    Don't have an account? 
                    <a href="https://form.fillout.com/t/bVrkgZUwVEus" target="_blank" @click="handleSignUpClick" class="text-primary hover:underline font-medium">Sign up here</a>
                </p>
            </div>
        </div>
    </AuthBase>
</template>
