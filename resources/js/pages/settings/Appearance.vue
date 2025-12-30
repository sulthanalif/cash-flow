<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
// Import tipe PageProps dari starter kit Yang Mulia
import { type AppPageProps } from '@/types';

import AppearanceTabs from '@/components/AppearanceTabs.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import Spinner from '@/components/ui/spinner/Spinner.vue';

const page = usePage<AppearanceProps>();
const authPermissions = page.props.auth.permissions;

const props = defineProps<{
    edited_by: number;
    updated_at: string;
}>();

const canEdit = computed(() =>
    Array.isArray(authPermissions) &&
    authPermissions.includes('appearance-edit')
);

interface AppearanceProps extends AppPageProps {
    name: string;
    icon: string | null;
    logo: string | null;
}

const form = useForm({
    name_app: page.props.name || '',
    icon_app: null as File | null,
    logo_app: null as File | null,
});

const iconPreview = ref<string | null>(page.props.icon);
const logoPreview = ref<string | null>(page.props.logo);

const handleIconChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.icon_app = file;
        iconPreview.value = URL.createObjectURL(file);
    }
};

const handleLogoChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.logo_app = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('appearance.update'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Settings updated. Refreshing...');
            
            form.icon_app = null; 
            form.logo_app = null;

            setTimeout(() => {
                window.location.reload();
            }, 1000); 
        },
        onError: () => toast.error('Failed to update settings.'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Appearance settings" />

        <SettingsLayout>
            <form @submit.prevent="submit" class="space-y-6">
                
                <div class="space-y-6">
                    <HeadingSmall
                        title="General Appearance"
                        description="Customize your application branding."
                    />
                    <AppearanceTabs />
                </div>

                <div class="border-b"></div>

                <div v-if="canEdit" class="space-y-6">
                    
                    <div class="space-y-2">
                        <Label for="name_app">Application Name</Label>
                        <Input
                            id="name_app"
                            type="text"
                            v-model="form.name_app"
                            placeholder="My Application"
                        />
                        <InputError :message="form.errors.name_app" />
                    </div>

                    <div class="space-y-4">
                        <div>
                            <Label class="text-base">App Icon</Label>
                            <p class="text-sm text-muted-foreground">
                                Used for browser tabs (favicon). Recommended 512x512 PNG.
                            </p>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div v-if="iconPreview" class="relative h-20 w-20 overflow-hidden rounded-xl border shadow-sm">
                                    <img :src="iconPreview" alt="Icon Preview" class="h-full w-full bg-white object-cover" />
                                </div>
                                <div v-else class="flex h-20 w-20 items-center justify-center rounded-xl border bg-gray-50 text-gray-400">
                                    <span class="text-xs">No Icon</span>
                                </div>
                            </div>

                            <div class="w-full max-w-sm">
                                <Input
                                    id="icon_app"
                                    type="file"
                                    accept="image/png, image/jpeg, image/x-icon"
                                    @change="handleIconChange"
                                    class="cursor-pointer"
                                />
                                <InputError :message="form.errors.icon_app" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <Label class="text-base">App Logo</Label>
                            <p class="text-sm text-muted-foreground">
                                Used in the sidebar and login screen.
                            </p>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div v-if="logoPreview" class="relative h-20 w-auto min-w-[5rem] overflow-hidden rounded-lg border bg-white p-1 shadow-sm">
                                    <img :src="logoPreview" alt="Logo Preview" class="h-full w-full object-contain" />
                                </div>
                                <div v-else class="flex h-20 w-20 items-center justify-center rounded-lg border bg-gray-50 text-gray-400">
                                    <span class="text-xs">No Logo</span>
                                </div>
                            </div>

                            <div class="w-full max-w-sm">
                                <Input
                                    id="logo_app"
                                    type="file"
                                    accept="image/*"
                                    @change="handleLogoChange"
                                    class="cursor-pointer"
                                />
                                <InputError :message="form.errors.logo_app" />
                            </div>
                        </div>

                        <div class="flex space-x-4 w-full max-w-sm space-y-2 mt-4">
                            <Label>Last Updated</Label>
                            <div>
                                <Label>{{ props.updated_at }}</Label>
                                <Label>{{ props.edited_by }}</Label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <Button type="submit" :disabled="form.processing">
                            <Spinner v-if="form.processing" class="animate-spin" />
                            <span>Save Changes</span>
                        </Button>
                    </div>

                </div>

            </form>
        </SettingsLayout>
    </AppLayout>
</template>