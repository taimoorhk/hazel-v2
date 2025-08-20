<script setup lang="ts">
import { useSidebar } from '@/components/ui/sidebar/utils';

const { state } = useSidebar();
</script>

<template>
    <div class="flex items-center gap-3">
        <img 
            :src="state === 'collapsed' ? '/hazel_watermark.png' : '/hazel_logo2.png'" 
            :alt="state === 'collapsed' ? 'Hazel Watermark' : 'Hazel Logo'" 
            class="h-8 w-auto flex-shrink-0" 
        />
        <span 
            v-show="state === 'expanded'" 
            class="font-semibold text-sidebar-foreground/70"
        >
            <!-- This text will be hidden when collapsed -->
        </span>
    </div>
</template>
