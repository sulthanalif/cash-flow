<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import SelectTags from '@/components/ui/select-tags/SelectTags.vue';
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

// --- PROPS & INTERFACE ---
const props = defineProps<{
    permissionOptions: { value: string; label: string }[];
}>();

interface Role {
    id: number;
    name: string;
    permissions: { id: number; name: string }[];
}

interface RoleForm {
    id: number;
    name: string;
    permissions: string[];
}

// --- STATE MANAGEMENT ---
const form = useForm<RoleForm>({
    id: 0,
    name: '',
    permissions: [],
});

const isSheetOpen = ref(false); // Mengganti isModalOpen
const titleSheet = ref(''); // Mengganti titleModal
const descriptionSheet = ref('');
const isLoading = ref(false);

// --- LOGIC ---
const onSubmit = () => {
    isLoading.value = true;
    const url = form.id ? route('roles.update', form.id) : route('roles.store');
    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                form.id ? 'Update Role Success' : 'Create Role Success',
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

const openCreateModal = () => {
    // Nama fungsi tetap sama agar parent tidak error
    form.reset();
    form.clearErrors();
    form.id = 0;
    titleSheet.value = 'Create New Role';
    descriptionSheet.value = 'Add a new role and assign permissions.';
    isSheetOpen.value = true;
};

const openEditModal = (role: Role) => {
    form.clearErrors();
    form.id = role.id;
    form.name = role.name;
    form.permissions = role.permissions.map((p) => p.name);
    titleSheet.value = 'Edit Role';
    descriptionSheet.value = 'Update role details and modify permissions.';
    isSheetOpen.value = true;
};

// Expose ke Parent
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
                    id="roleForm"
                    @submit.prevent="onSubmit"
                    class="flex flex-col gap-6"
                >
                    <div class="space-y-2">
                        <Label for="name">Role Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            placeholder="e.g. Super Admin"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="permissions">Permissions</Label>
                        <SelectTags
                            v-model="form.permissions"
                            :options="props.permissionOptions"
                            placeholder="Select permissions..."
                        />
                        <InputError :message="form.errors.permissions" />
                        <p class="text-[0.8rem] text-muted-foreground">
                            Select the permissions allowed for this role.
                        </p>
                    </div>
                </form>
            </div>
            <SheetFooter
                class="mt-auto px-6 py-4 sm:justify-between dark:bg-transparent"
            >
                <Button type="submit" form="roleForm" :disabled="isLoading">
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
