<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const breadcrumbs = [
  { title: 'Elderly Profiles', href: '/elderly-profiles' },
];
</script>

<template>
  <Head title="Elderly Profiles" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-8">
      <h1 class="text-2xl font-bold mb-4">Elderly Profiles</h1>
      <p class="text-muted-foreground">This is the Elderly Profiles page. Add your content here.</p>
    </div>
  </AppLayout>
</template> 