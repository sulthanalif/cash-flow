<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';


const page = usePage();

const appLogo = computed(() => page.props.logo as string | null);
const appName = computed(() => page.props.name as string || 'Laravel');
</script>

<template>
    <img
        v-if="appLogo"
        :src="appLogo"
        :alt="appName"
        class="h-10 w-auto flex-shrink-0 object-contain transition-transform duration-300 hover:scale-105"
    />

    <div
        v-else
        class="flex aspect-square size-8 flex-shrink-0 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>


    <div class="flex flex-col gap-0.5 leading-none truncate ml-2">
        <span class="font-semibold">{{ appName }}</span>
    </div>

    </template>
