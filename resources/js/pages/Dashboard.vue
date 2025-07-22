<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardAction } from '@/components/ui/card';
import Icon from '@/components/Icon.vue';
import { VueUiWheel } from 'vue-data-ui';
import { VueUiTiremarks } from 'vue-data-ui';
import { ref } from 'vue';
import { useAuthGuard } from '@/composables/useAuthGuard';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { Link } from '@inertiajs/vue3';
useAuthGuard();
const { user } = useSupabaseUser();

function getRandomScore() {
  return Math.floor(Math.random() * 81) + 10; // 10-90 for visible movement
}
function getRandomChange() {
  return parseFloat((Math.random() * 20 - 10).toFixed(1));
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const stats = [
    {
        title: 'Elderly Profiles',
        value: 14,
        change: -10.6,
        icon: 'users',
    },
    {
        title: 'Total Calls',
        value: 22,
        change: 27.5,
        icon: 'phone',
    },
    {
        title: 'Minutes Used',
        value: 16,
        change: -1.6,
        icon: 'clock',
    },
    {
        title: 'Credits Remaining',
        value: 20,
        change: 12.3,
        icon: 'wallet',
    },
];

const elderlyProfiles = [
  { name: 'Alice Johnson', score: 85, change: 2.5 },
  { name: 'Bob Smith', score: 72, change: -1.2 },
  { name: 'Clara Lee', score: 90, change: 3.1 },
  { name: 'David Kim', score: 65, change: -0.8 },
  { name: 'Emma Brown', score: 78, change: 1.7 },
  { name: 'Frank Green', score: 88, change: 2.0 },
];

const baseWheelConfig = {
    responsive: false,
    theme: undefined,
    style: {
        fontFamily: 'inherit',
        chart: {
            backgroundColor: '#ffffffff',
            color: '#1A1A1Aff',
            animation: {
                use: true,
                speed: 0.5,
                acceleration: 1
            },
            layout: {
                wheel: {
                    ticks: {
                        type: 'classic' as const,
                        rounded: true,
                        inactiveColor: '#e1e5e8',
                        activeColor: '#00a53d',
                        sizeRatio: 0.7,
                        quantity: 100,
                        strokeWidth: 5,
                        gradient: {
                            show: true,
                            shiftHueIntensity: 10
                        }
                    }
                },
                innerCircle: {
                    show: false,
                    stroke: '#e1e5e8ff',
                    strokeWidth: 1
                },
                percentage: {
                    show: true,
                    fontSize: 48,
                    rounding: 0,
                    bold: true,
                    formatter: null
                }
            }
        }
    },
    userOptions: {
        show: false,
        showOnChartHover: false,
        keepStateOnChartLeave: false,
        position: 'right' as const,
        buttons: {
            tooltip: false,
            pdf: false,
            csv: false,
            img: false,
            table: false,
            labels: false,
            fullscreen: false,
            sort: false,
            stack: false,
            animation: false,
            annotator: false
        },
        callbacks: {
            animation: null,
            annotator: null,
            csv: null,
            fullscreen: null,
            img: null,
            labels: null,
            pdf: null,
            sort: null,
            stack: null,
            table: null,
            tooltip: null
        },
        buttonTitles: {
            open: 'Open options',
            close: 'Close options',
            pdf: 'Download PDF',
            img: 'Download PNG',
            fullscreen: 'Toggle fullscreen',
            annotator: 'Toggle annotator'
        },
        print: {
            allowTaint: false,
            backgroundColor: '#FFFFFFff',
            useCORS: false,
            onclone: null,
            scale: 2,
            logging: false
        }
    }
};

const tiremarksConfig = ref({
    theme: undefined,
    style: {
        fontFamily: 'inherit',
        chart: {
            backgroundColor: '#FFFFFFff',
            color: '#1A1A1Aff',
            animation: {
                use: true,
                speed: 0.5,
                acceleration: 1
            },
            layout: {
                display: 'horizontal' as 'horizontal',
                crescendo: false,
                curved: false,
                curveAngleX: 10,
                curveAngleY: 10,
                activeColor: '#00a53d',
                inactiveColor: '#e1e5e8',
                ticks: {
                    gradient: {
                        show: true,
                        shiftHueIntensity: 10
                    }
                }
            },
            percentage: {
                show: true,
                useGradientColor: true,
                color: '#1A1A1Aff',
                fontSize: 16,
                bold: true,
                rounding: 0,
                verticalPosition: 'bottom' as 'bottom',
                horizontalPosition: 'left' as 'left',
                formatter: null
            },
            
        }
    },
    userOptions: {
        show: false,
        showOnChartHover: false,
        keepStateOnChartLeave: false,
        position: 'right' as 'right',
        buttons: {
            tooltip: false,
            pdf: false,
            csv: false,
            img: false,
            table: false,
            labels: false,
            fullscreen: false,
            sort: false,
            stack: false,
            animation: false,
            annotator: false
        },
        callbacks: {
            animation: null,
            annotator: null,
            csv: null,
            fullscreen: null,
            img: null,
            labels: null,
            pdf: null,
            sort: null,
            stack: null,
            table: null,
            tooltip: null
        },
        buttonTitles: {
            open: 'Open options',
            close: 'Close options',
            pdf: 'Download PDF',
            img: 'Download PNG',
            fullscreen: 'Toggle fullscreen',
            annotator: 'Toggle annotator'
        },
        print: {
            allowTaint: false,
            backgroundColor: '#FFFFFFff',
            useCORS: false,
            onclone: null,
            scale: 2,
            logging: false
        }
    }
});
</script>

<template>
    <AppLayout>
        <Head title="Dashboard" />
        <!-- Removed debug output -->
        <div v-if="user" class="p-6">
            <h1 class="text-2xl font-bold mb-4">Welcome, {{ user?.user_metadata?.name || user?.email || 'User' }}!</h1>
            <!-- Restored Dashboard UI -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <Card v-for="stat in stats" :key="stat.title">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                        <Icon :name="stat.icon" class="h-5 w-5 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stat.value }}</div>
                        <div :class="{'text-green-600': stat.change > 0, 'text-red-600': stat.change < 0, 'text-muted-foreground': stat.change === 0 }" class="text-xs">
                            {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}%
                        </div>
                    </CardContent>
                </Card>
            </div>
            <div class="flex flex-row gap-6 mb-8">
                <div class="w-[70%]">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Usage Overview</h2>
                        <Link href="/elderly-profiles" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">All Profiles</Link>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card v-for="profile in elderlyProfiles" :key="profile.name" class="flex flex-col items-center py-6">
                            <span class="mb-2 font-medium">{{ profile.name }}</span>
                            <VueUiWheel :dataset="{ value: profile.score, label: profile.name, percentage: profile.score }" :config="baseWheelConfig" />
                        </Card>
                    </div>
                </div>
                <div class="w-[30%]">
                    <Card>
                        <CardHeader>
                            <CardTitle>Call Trends</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <VueUiTiremarks :dataset="{ value: stats[1].value, label: stats[1].title, percentage: 100 }" :config="tiremarksConfig" />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
        <div v-else class="flex items-center justify-center h-96">
            <span class="text-lg text-red-500">Session error. Please log in again.</span>
        </div>
    </AppLayout>
</template>
