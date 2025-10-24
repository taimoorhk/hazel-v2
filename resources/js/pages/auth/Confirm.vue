<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderCircle, CheckCircle, XCircle } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import { router } from '@inertiajs/vue3';
const loading = ref(false);
const errorMsg = ref('');
const successMsg = ref('');
const isConfirmed = ref(false);



// Get URL parameters
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');
const type = urlParams.get('type');
const redirectTo = urlParams.get('redirect_to');

console.log('URL params:', { token, type, redirectTo });
console.log('Full URL:', window.location.href);
console.log('Search params:', window.location.search);

const confirmMagicLink = async () => {
    if (!token || type !== 'magiclink') {
        errorMsg.value = `Invalid confirmation link. Missing parameters: token=${token}, type=${type}`;
        return;
    }

    loading.value = true;
    errorMsg.value = '';

    try {
        // For Magic Links, we need to exchange the token for a session
        // Using the correct API for Magic Link verification
        const { data, error } = await supabase.auth.verifyOtp({
            token: token,
            type: 'email',
        } as any); // Type assertion to bypass TypeScript strict checking

        if (error) {
            errorMsg.value = error.message;
        } else if (data && data.user) {
            console.log('User data from Supabase:', data.user);
            
            // Sync user with Laravel database
            try {
                const createResponse = await fetch('/api/create-user-from-supabase', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: data.user.email,
                        supabase_id: data.user.id,
                        name: data.user.user_metadata?.name || data.user.user_metadata?.display_name || 'User',
                        role: data.user.user_metadata?.role || 'Normal User',
                        user_questions: data.user.user_metadata?.user_questions || null,
                    })
                });

                if (createResponse.ok) {
                    const createResult = await createResponse.json();
                    console.log('User sync successful:', createResult);
                } else {
                    const createError = await createResponse.text();
                    console.error('User sync failed:', createError);
                    
                    // Try alternative sync method if the first one fails
                    try {
                        console.log('Attempting alternative sync method...');
                        const syncResponse = await fetch('/api/sync-from-supabase-to-laravel', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                email: data.user.email
                            })
                        });
                        
                        if (syncResponse.ok) {
                            const syncResult = await syncResponse.json();
                            console.log('Alternative sync successful:', syncResult);
                        } else {
                            const syncError = await syncResponse.text();
                            console.error('Alternative sync also failed:', syncError);
                        }
                    } catch (syncError) {
                        console.error('Alternative sync error:', syncError);
                    }
                }
            } catch (createError) {
                console.error('User sync error:', createError);
                
                // Try alternative sync method if the first one fails
                try {
                    console.log('Attempting alternative sync method after error...');
                    const syncResponse = await fetch('/api/sync-from-supabase-to-laravel', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            email: data.user.email
                        })
                    });
                    
                    if (syncResponse.ok) {
                        const syncResult = await syncResponse.json();
                        console.log('Alternative sync successful:', syncResult);
                    } else {
                        const syncError = await syncResponse.text();
                        console.error('Alternative sync also failed:', syncError);
                    }
                } catch (syncError) {
                    console.error('Alternative sync error:', syncError);
                }
            }

            successMsg.value = 'Email confirmed successfully!';
            isConfirmed.value = true;
            
            // Redirect to dashboard after a short delay
            setTimeout(() => {
                router.visit('/dashboard');
            }, 1500);
        }
    } catch (e) {
        errorMsg.value = 'Network error: Unable to confirm email.';
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    console.log('Confirm page mounted');
    console.log('Current URL:', window.location.href);
    console.log('Search params:', window.location.search);
    
    // Check if we have a session already (user might be already logged in)
    const { data } = await supabase.auth.getSession();
    if (data.session) {
        console.log('User already has a session, redirecting to dashboard');
        router.visit('/dashboard');
        return;
    }
    
    // Auto-confirm when page loads with valid parameters
    if (token && type === 'magiclink') {
        console.log('Valid parameters found, confirming...');
        confirmMagicLink();
    } else {
        console.log('Invalid parameters:', { token, type });
        errorMsg.value = `Invalid confirmation link. Missing parameters: token=${token}, type=${type}. Please try logging in again.`;
    }
});
</script>

<template>
    <AuthBase>
        <Head title="Confirm Email" />
        
        <div class="text-center space-y-6">
            <!-- Loading State -->
            <div v-if="loading" class="space-y-4">
                <LoaderCircle class="animate-spin h-16 w-16 text-primary mx-auto" />
                <h2 class="text-2xl font-bold text-gray-900">Confirming your email...</h2>
                <p class="text-gray-600">Please wait while we verify your magic link.</p>
            </div>

            <!-- Success State -->
            <div v-else-if="isConfirmed" class="space-y-4">
                <CheckCircle class="h-16 w-16 text-green-500 mx-auto" />
                <h2 class="text-2xl font-bold text-gray-900">Email confirmed!</h2>
                <p class="text-gray-600">{{ successMsg }}</p>
                <p class="text-sm text-gray-500">Redirecting to dashboard...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="errorMsg" class="space-y-4">
                <XCircle class="h-16 w-16 text-red-500 mx-auto" />
                <h2 class="text-2xl font-bold text-gray-900">Confirmation failed</h2>
                <p class="text-red-600">{{ errorMsg }}</p>
                <div class="p-4 rounded-lg text-xs" style="background-color: #f9fafb;">
                    <p><strong>Debug Info:</strong></p>
                    <p>Token: {{ token || 'Not found' }}</p>
                    <p>Type: {{ type || 'Not found' }}</p>
                    <p>Check browser console for more details</p>
                </div>
                <div class="space-y-2">
                    <Button @click="confirmMagicLink" :disabled="loading" class="w-full">
                        <LoaderCircle v-if="loading" class="animate-spin h-4 w-4 mr-2" />
                        Try again
                    </Button>
                    <Button @click="router.visit('/login')" variant="outline" class="w-full">
                        Back to login
                    </Button>
                </div>
            </div>

            <!-- Default State -->
            <div v-else class="space-y-4">
                <LoaderCircle class="animate-spin h-16 w-16 text-primary mx-auto" />
                <h2 class="text-2xl font-bold text-gray-900">Processing...</h2>
                <p class="text-gray-600">Please wait...</p>
            </div>
        </div>
    </AuthBase>
</template> 