<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';

const { user } = useSupabaseUser(); // user is of type User | null from Supabase
const { isMobile, state } = useSidebar();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg" class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground flex items-center gap-2">
                        <template v-if="user">
                            <img v-if="user.user_metadata?.avatar_url" :src="user.user_metadata.avatar_url" alt="avatar" class="w-8 h-8 rounded-full object-cover border border-neutral-200" />
                            <div v-else class="w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-500 font-bold">
                                {{ user.user_metadata?.display_name?.charAt(0) || (user.email ? user.email.charAt(0) : '?') }}
                            </div>
                            <span class="font-medium text-sm text-neutral-800 truncate max-w-[120px]">{{ user.user_metadata?.display_name || user.email || '' }}</span>
                        </template>
                        <ChevronsUpDown class="ml-auto size-4" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-(--reka-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end"
                    :side-offset="4"
                >
                    <UserMenuContent v-if="user" :user="user as any" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
