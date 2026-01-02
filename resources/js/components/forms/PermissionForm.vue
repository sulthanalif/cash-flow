<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Spinner from '../ui/spinner/Spinner.vue';

// Interface Data
interface Permission {
    id: number;
    name: string;
}

// Interface Form
interface PermissionForm {
    id: number;
    name: string;
}

// State Form
const form = useForm<PermissionForm>({
    id: 0,
    name: '',
});

const isSheetOpen = ref(false);
const titleSheet = ref('');
const descriptionSheet = ref('');
const isLoading = ref(false);

const onSubmit = () => {
    isLoading.value = true;
    const url = form.id
        ? route('permissions.update', form.id)
        : route('permissions.store');
    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                form.id
                    ? 'Update Permission Success'
                    : 'Create Permission Success',
            );
            form.reset();
            form.clearErrors();
            closeSheet();
        },
        onError: (errors: any) => {
            if (
                errors.message &&
                !Object.keys(errors).some((k) => k !== 'message')
            ) {
                toast.error(errors.message);
            }
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

const closeSheet = () => {
    isSheetOpen.value = false;
    setTimeout(() => {
        form.reset();
        form.clearErrors();
    }, 300);
};

// --- Exposed Methods ---

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.id = 0;
    titleSheet.value = 'Create Permission';
    descriptionSheet.value = 'Add a new permission to the system.';
    isSheetOpen.value = true;
};

const openEditModal = (permission: Permission) => {
    form.clearErrors();
    form.id = permission.id;
    form.name = permission.name;
    titleSheet.value = 'Edit Permission';
    descriptionSheet.value = 'Modify the permission name.';
    isSheetOpen.value = true;
};

defineExpose({
    openCreateModal,
    openEditModal,
});
</script>

<template>
    <Sheet v-model:open="isSheetOpen">
        <SheetContent class="flex h-full w-full flex-col gap-0 p-0 sm:max-w-md">
            <SheetHeader class="border-b px-6 py-6">
                <SheetTitle>{{ titleSheet }}</SheetTitle>
                <SheetDescription>
                    {{ descriptionSheet }}
                </SheetDescription>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <form
                    id="permissionForm"
                    @submit.prevent="onSubmit"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name" class="font-semibold"
                            >Permission Name</Label
                        >
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            placeholder="e.g. user-create"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" />
                        <p class="text-[0.8rem] text-muted-foreground">
                            Use a convention like <code>resource-action</code>
                            (e.g., post-edit).
                        </p>
                    </div>
                </form>
            </div>

            <SheetFooter
                class="mt-auto  px-6 py-4 sm:justify-between dark:bg-transparent"
            >
                <Button
                    type="submit"
                    form="permissionForm"
                    :disabled="isLoading"
                >
                    <Spinner v-if="isLoading" class="animate-spin" />
                    Save
                </Button>
                <Button type="button" variant="ghost" @click="closeSheet">
                    Cancel
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
