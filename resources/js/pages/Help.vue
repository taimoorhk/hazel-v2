<template>
    <AppSidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Help & Support</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Find answers to common questions and get support</p>
                </div>
            </div>
        </template>

        <div class="w-full px-6 lg:px-8 xl:px-12 space-y-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">FAQ</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">Find answers to frequently asked questions</p>
                    <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                        Browse FAQ
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Documentation</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">Detailed guides and tutorials</p>
                    <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm font-medium">
                        View Docs
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Contact Support</h3>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-4">Get help from our support team</p>
                    <button class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors text-sm font-medium">
                        Contact Us
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Frequently Asked Questions</h2>
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            How do I get started?
                        </h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 ml-7">After signing up, you can immediately start using the platform. Navigate to the Dashboard to see your overview and begin managing your elderly profiles.</p>
                    </div>
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            How do I add elderly profiles?
                        </h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 ml-7">Go to the "Elderly Profiles" section in the sidebar. Click the "Add New Profile" button and fill in the required information.</p>
                    </div>
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Can I export my data?
                        </h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 ml-7">Yes, you can export your data in various formats. Go to Settings > Data Export to download your information.</p>
                    </div>
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            How secure is my data?
                        </h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 ml-7">We use industry-standard encryption and security measures to protect your data. All information is stored securely and access is strictly controlled.</p>
                    </div>
                    <div class="border border-neutral-200 dark:border-neutral-700 rounded-lg p-4 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
                        <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-2 flex items-center">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            What payment methods do you accept?
                        </h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400 ml-7">We accept all major credit cards, PayPal, and bank transfers for enterprise plans. Visit the Billing section to manage your payment methods.</p>
                    </div>
                </div>
            </div>

            <!-- Contact Information and System Status Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Contact Information -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">Contact Information</h2>
                        <div class="w-8 h-8 bg-teal-100 dark:bg-teal-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-1">Email Support</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">support@hazel.com</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-500">Response within 24 hours</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-neutral-900 dark:text-neutral-100 mb-1">Phone Support</h3>
                                <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-1">+1 (555) 123-4567</p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-500">Monday - Friday, 9AM - 5PM EST</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-neutral-900 dark:text-neutral-100">System Status</h2>
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">All systems operational</span>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-green-700 dark:text-green-300">No incidents reported</span>
                            </div>
                        </div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-500">
                            Last updated: {{ new Date().toLocaleString() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
</script> 