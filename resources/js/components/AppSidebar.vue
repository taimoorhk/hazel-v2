<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, BarChart3, Shield } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';
import { useSupabaseUser } from '@/composables/useSupabaseUser';
import { computed } from 'vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Reports',
        href: '/reports',
        icon: BarChart3,
    },
    {
        title: 'Elderly Profiles',
        href: '/elderly-profiles',
        icon: Users,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Billing',
        href: '/billing',
        icon: Folder,
    },
    {
        title: 'Help',
        href: '/help',
        icon: BookOpen,
    },
];

const { user } = useSupabaseUser();
const filteredMainNavItems = computed(() => {
  const meta = user.value?.user_metadata;
  let items = [...mainNavItems];
  
  // Add admin panel for admin users
  if (meta && (meta.role === 'Admin' || meta.role === 'Administration')) {
    items.push({
      title: 'Admin Panel',
      href: '/admin',
      icon: Shield,
    });
  }
  
  if (meta && meta.role === 'Normal User') {
    items = items.filter(item => item.title !== 'Elderly Profiles');
  }
  if (meta && meta.role === 'Organization') {
    items = items.filter(item => item.title !== 'Reports');
  }
  return items;
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredMainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
