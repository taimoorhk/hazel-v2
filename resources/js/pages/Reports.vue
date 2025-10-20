<template>
    <AppSidebarLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">Reports & Analytics</h1>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">View detailed reports and insights about your elderly care activities</p>
                </div>
            </div>
        </template>

        <div class="w-full px-6 lg:px-8 xl:px-12 space-y-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Total Calls</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">127</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400">▲ 12%</span>
                        <span class="text-neutral-600 dark:text-neutral-400 ml-2">from last month</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Avg. Call Duration</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">24m</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400">▲ 8%</span>
                        <span class="text-neutral-600 dark:text-neutral-400 ml-2">from last month</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Satisfaction Score</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">4.8</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-green-600 dark:text-green-400">▲ 0.2</span>
                        <span class="text-neutral-600 dark:text-neutral-400 ml-2">from last month</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Active Profiles</p>
                            <p class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">3</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <span class="text-neutral-600 dark:text-neutral-400">No change</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Call Activity Chart -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Call Activity</h3>
                        <div class="flex space-x-2">
                            <button class="px-3 py-1 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-md">Week</button>
                            <button class="px-3 py-1 text-sm text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md">Month</button>
                            <button class="px-3 py-1 text-sm text-neutral-600 dark:text-neutral-400 hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-md">Year</button>
                        </div>
                    </div>
                    <div class="h-64 flex items-end justify-between space-x-2">
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 60%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Mon</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 80%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Tue</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 45%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Wed</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 90%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Thu</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 70%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Fri</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 55%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Sat</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-8 bg-blue-500 rounded-t" style="height: 40%"></div>
                            <span class="text-xs text-neutral-600 dark:text-neutral-400 mt-2">Sun</span>
                        </div>
                    </div>
                </div>

                <!-- Call Duration Distribution -->
                <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-6">Call Duration Distribution</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">0-10 min</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 15%"></div>
                                </div>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">15%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">10-20 min</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-orange-500 h-2 rounded-full" style="width: 35%"></div>
                                </div>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">35%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">20-30 min</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: 40%"></div>
                                </div>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">40%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-neutral-600 dark:text-neutral-400">30+ min</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-24 bg-neutral-200 dark:bg-neutral-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 10%"></div>
                                </div>
                                <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">10%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100">Recent Call Activity</h3>
                    <button class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-neutral-200 dark:border-neutral-700">
                                <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600 dark:text-neutral-400">Profile</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600 dark:text-neutral-400">Date</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600 dark:text-neutral-400">Duration</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600 dark:text-neutral-400">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-neutral-600 dark:text-neutral-400">Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">M</span>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Mary Johnson</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">Today, 2:30 PM</td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">28 min</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Completed
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <span class="text-sm text-neutral-600 dark:text-neutral-400 mr-1">5.0</span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-green-600 dark:text-green-400">R</span>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Robert Smith</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">Yesterday, 4:15 PM</td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">22 min</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Completed
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <span class="text-sm text-neutral-600 dark:text-neutral-400 mr-1">4.8</span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b border-neutral-100 dark:border-neutral-800">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">E</span>
                                        </div>
                                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">Elizabeth Davis</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">2 days ago, 1:45 PM</td>
                                <td class="py-3 px-4 text-sm text-neutral-600 dark:text-neutral-400">35 min</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        Completed
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <span class="text-sm text-neutral-600 dark:text-neutral-400 mr-1">5.0</span>
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white dark:bg-neutral-900 rounded-xl border border-neutral-200 dark:border-neutral-800 p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-neutral-900 dark:text-neutral-100 mb-2">Export Reports</h3>
                        <p class="text-sm text-neutral-600 dark:text-neutral-400">Download your reports in various formats</p>
                    </div>
                    <div class="flex space-x-3">
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export PDF
                        </button>
                        <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>

<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { router as inertiaRouter } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const { user } = useSupabaseUser();

onMounted(() => {
    // Check if user has Organization role and redirect if so
    const meta = user.value?.user_metadata;
    if (meta && meta.role === 'Organization') {
        // Redirect to dashboard with a message
        inertiaRouter.visit('/dashboard', {
            data: {
                message: 'Access denied. Reports are not available for Organization users.'
            }
        });
    }
});
</script> 