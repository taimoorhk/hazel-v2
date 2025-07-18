<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardHeader, CardTitle, CardContent, CardAction } from '@/components/ui/card';
import Icon from '@/components/Icon.vue';
import { VueUiWheel } from 'vue-data-ui';
import { VueUiTiremarks } from 'vue-data-ui';
import { ref } from 'vue';

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
  { name: 'Alice Johnson', score: getRandomScore(), change: getRandomChange() },
  { name: 'Bob Smith', score: getRandomScore(), change: getRandomChange() },
  { name: 'Clara Lee', score: getRandomScore(), change: getRandomChange() },
  { name: 'David Kim', score: getRandomScore(), change: getRandomChange() },
  { name: 'Emma Brown', score: getRandomScore(), change: getRandomChange() },
  { name: 'Frank Green', score: getRandomScore(), change: getRandomChange() },
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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mb-2">
                <span class="block text-2xl sm:text-3xl font-semibold tracking-tight">
                    Welcome back,
                    <span class="text-neutral-400 font-semibold">{{ $page.props.auth.user.name.split(' ')[0] }}.</span>
                </span>
            </div>
            <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
                <Card v-for="(stat, i) in stats" :key="i" class="flex flex-row items-center justify-between bg-white/80 dark:bg-neutral-900/80 border-none shadow-[0_2px_12px_0_rgba(0,0,0,0.07)] rounded-[18px] p-4 min-h-0 h-[110px]">
                    <div class="flex flex-col gap-1 justify-center">
                        <CardTitle class="text-sm font-medium text-muted-foreground mb-0">{{ stat.title }}</CardTitle>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-bold text-black dark:text-white">{{ stat.value }}</span>
                            <span class="flex items-center gap-1 text-xs font-medium" :class="stat.change > 0 ? 'text-green-600' : 'text-red-500'">
                                {{ Math.abs(stat.change) }}%
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/20 h-11 w-11">
                        <Icon :name="stat.icon" class="h-6 w-6 text-green-600 dark:text-green-400" />
                    </div>
                </Card>
            </div>
            <div class="relative flex flex-row min-h-[300px] flex-1 gap-3 bg-transparent border-none shadow-none">
                <div class="flex-1 basis-7/10 p-0 rounded-[18px] bg-white dark:bg-neutral-900 flex flex-col">
                    <div class="flex items-center justify-between mb-6 px-4 pt-6">
                        <span class="text-lg font-semibold">Profile Statistics</span>
                        <a href="/elderly-profiles" class="px-4 py-1 rounded-md bg-primary text-white text-sm font-medium shadow hover:bg-primary/90 transition focus:outline-none focus:ring-2 focus:ring-primary/50">All Profiles</a>
                    </div>
                    <div class="grid grid-cols-3 gap-3 flex-1 px-4 pb-6">
                        <div v-for="(profile, i) in elderlyProfiles" :key="i" class="flex flex-col items-center justify-center rounded-[14px] bg-white dark:bg-neutral-900 shadow-[0_2px_8px_0_rgba(0,0,0,0.06)] p-3 min-w-0">
                            <!-- eslint-disable-next-line -->
                            <VueUiWheel
                                :dataset="{ percentage: profile.score }"
                                :config="{
                                    ...baseWheelConfig,
                                    style: {
                                        ...baseWheelConfig.style,
                                        chart: {
                                            ...baseWheelConfig.style.chart,
                                            title: {
                                                text: profile.name,
                                                color: '#1A1A1Aff',
                                                fontSize: 20,
                                                bold: true,
                                                textAlign: 'center',
                                                paddingLeft: 0,
                                                paddingRight: 0,
                                                subtitle: {
                                                    color: '#A1A1A1ff',
                                                    text: '',
                                                    fontSize: 16,
                                                    bold: false
                                                }
                                            }
                                        }
                                    }
                                }"
                                :animation="true"
                            />
                            <span class="mt-1 text-xs" :class="profile.change > 0 ? 'text-green-600' : 'text-red-500'">
                                {{ profile.change > 0 ? '+' : '' }}{{ profile.change }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="basis-3/10 flex flex-col gap-4 h-full justify-between">
                    <!-- Plan Details Box -->
                    <div class="flex-1 flex flex-col justify-between bg-white dark:bg-neutral-900 rounded-[18px] shadow-[0_2px_12px_0_rgba(0,0,0,0.07)] p-4 mb-2">
                        <div>
                            <div class="text-lg font-bold mb-0.5">Call Credits</div>
                            <div class="text-muted-foreground text-sm mb-0.5">Available minutes for phone calls</div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-muted-foreground font-medium text-xs">Available</span>
                                <span class="font-semibold text-muted-foreground text-xs">500 / 5 minutes</span>
                            </div>
                            <div class="w-full flex items-center mb-1">
                                <div class="w-full">
                                    <VueUiTiremarks :dataset="{ percentage: 100 }" :config="tiremarksConfig" />
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center justify-center rounded-full bg-green-100 text-green-600 h-8 w-8">
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='w-5 h-5'><path stroke-linecap='round' stroke-linejoin='round' d='M2.25 6.75v10.5A2.25 2.25 0 004.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75M2.25 6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25M2.25 6.75h19.5' /></svg>
                                </span>
                                <span class="text-base font-semibold">7 of 7</span>
                                <span class="text-muted-foreground ml-0.5 text-xs">Profiles Ready</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center justify-center rounded-full bg-blue-100 text-blue-600 h-8 w-8">
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='w-5 h-5'><path stroke-linecap='round' stroke-linejoin='round' d='M12 6v6l4 2' /></svg>
                                </span>
                                <span class="text-base font-semibold">0</span>
                                <span class="text-muted-foreground ml-0.5 text-xs">Minutes Used</span>
                            </div>
                        </div>
                    </div>
                    <!-- Second Box Placeholder -->
                    <div class="flex-1 flex flex-col justify-center bg-white dark:bg-neutral-900 rounded-[18px] shadow-[0_2px_12px_0_rgba(0,0,0,0.07)] p-6 mt-2">
                        <div class="flex items-center justify-center text-muted-foreground">(Reserved for future content)</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
