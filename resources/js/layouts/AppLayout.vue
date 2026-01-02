<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import 'vue-sonner/style.css'
import { Toaster } from '@/components/ui/sonner'
import { toast } from 'vue-sonner';
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';


interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

const page = usePage();

onMounted(() => {
    if (page.props.flash?.success) {
        // console.log('✅ Triggering Success Toast');
        toast.success(page.props.flash.success);
    }
    if (page.props.flash?.error) {
        // console.log('❌ Triggering Error Toast');
        toast.error(page.props.flash.error);
    }
});

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster />
</template>
