<template>
    <AppSidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Billing</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Manage your subscription and billing information</p>
                </div>
            </div>
        </template>

        <div class="w-full px-6 lg:px-8 xl:px-12 space-y-8">
            <!-- Current Plan and Billing History Row -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <!-- Current Plan Section -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Current Plan</h2>
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Free Plan</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">Basic features included</p>
                            </div>
                            <div class="text-right">
                                <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">$0</p>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400">per month</p>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-neutral-200 dark:border-neutral-700">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-neutral-600 dark:text-neutral-400">Next billing date</span>
                                <span class="font-medium text-neutral-900 dark:text-neutral-100">Never</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing History Section -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Billing History</h2>
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-3 px-4 bg-neutral-50 dark:bg-neutral-800 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Free Plan</p>
                                <p class="text-xs text-neutral-600 dark:text-neutral-400">January 2025</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-neutral-900 dark:text-neutral-100">$0.00</p>
                                <div class="flex items-center space-x-1">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <p class="text-xs text-green-600 dark:text-green-400">Paid</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-2">
                            <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                View all invoices
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method and Usage Stats Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Method Section -->
                <div class="lg:col-span-2 bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Payment Method</h2>
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex items-center justify-center py-8">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-neutral-100 dark:bg-neutral-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100 mb-2">No payment method</h3>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-6">Add a payment method to upgrade your plan</p>
                            <button class="inline-flex items-center px-6 py-3 bg-neutral-900 dark:bg-neutral-100 text-neutral-100 dark:text-neutral-900 rounded-lg hover:bg-neutral-800 dark:hover:bg-neutral-200 transition-colors font-medium">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Payment Method
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Usage Statistics -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Usage Statistics</h2>
                        <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-neutral-900 dark:text-neutral-100 mb-1">3</div>
                            <div class="text-sm text-neutral-600 dark:text-neutral-400">Elderly Profiles</div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">Storage Used</span>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">2.4 GB / 5 GB</span>
                            </div>
                            <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 48%"></div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-neutral-600 dark:text-neutral-400">API Calls</span>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">1,247 / 2,000</span>
                            </div>
                            <div class="w-full bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 62%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upgrade Plan Section -->
            <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Upgrade Your Plan</h2>
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <!-- Pro Plan -->
                    <div class="border-2 border-neutral-200 dark:border-neutral-700 rounded-xl p-6 hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Pro Plan</h3>
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs font-medium rounded-full">Popular</span>
                        </div>
                        <div class="mb-6">
                            <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">$29<span class="text-lg font-normal text-neutral-600 dark:text-neutral-400">/month</span></p>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Billed monthly</p>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Advanced analytics
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Priority support
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom integrations
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Unlimited profiles
                            </li>
                        </ul>
                        <button class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                            Upgrade to Pro
                        </button>
                    </div>

                    <!-- Enterprise Plan -->
                    <div class="border-2 border-neutral-200 dark:border-neutral-700 rounded-xl p-6 hover:border-purple-300 dark:hover:border-purple-600 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Enterprise Plan</h3>
                            <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 text-xs font-medium rounded-full">Enterprise</span>
                        </div>
                        <div class="mb-6">
                            <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">$99<span class="text-lg font-normal text-neutral-600 dark:text-neutral-400">/month</span></p>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Billed monthly</p>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Everything in Pro
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Dedicated support
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Custom features
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                SLA guarantee
                            </li>
                        </ul>
                        <button class="w-full px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-medium">
                            Contact Sales
                        </button>
                    </div>

                    <!-- Team Plan -->
                    <div class="border-2 border-neutral-200 dark:border-neutral-700 rounded-xl p-6 hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Team Plan</h3>
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 text-xs font-medium rounded-full">New</span>
                        </div>
                        <div class="mb-6">
                            <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">$49<span class="text-lg font-normal text-neutral-600 dark:text-neutral-400">/month</span></p>
                            <p class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">Billed monthly</p>
                        </div>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Up to 5 team members
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Shared workspace
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Team analytics
                            </li>
                            <li class="flex items-center text-sm text-neutral-700 dark:text-neutral-300">
                                <svg class="w-4 h-4 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Priority support
                            </li>
                        </ul>
                        <button class="w-full px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors font-medium">
                            Start Team Plan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
</script> 