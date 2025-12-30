<script setup lang="ts">
import { h, ref, computed } from 'vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable, { type ActionItem } from '@/components/ui/data-table/DataTable.vue';
import { type ColumnDef } from '@tanstack/vue-table';
import { Pencil, Trash } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/AlertDialog.vue';

// Import Child Form
import PermissionForm from '../../components/forms/PermissionForm.vue';

interface Permission {
    id: number
    name: string
}

const page = usePage();
const authPermissions = page.props.auth.permissions;

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('permission-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('permission-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('permission-delete'));

const props = defineProps<{ permissions: Permission[] }>();

// Ref ke Child Component
const permissionFormRef = ref<InstanceType<typeof PermissionForm> | null>(null);

// --- DELETE LOGIC (Di Parent) ---
const deleteForm = useForm({
    id: 0,
    name: ''
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

const openDeleteAlert = (permission: Permission) => {
    deleteForm.id = permission.id;
    deleteForm.name = permission.name;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('permissions.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Delete Permission Success');
            isDeleteOpen.value = false;
        },
        onError: (errors) => {
            if (errors.message) toast.error(errors.message);
        },
        onFinish: () => {
            isDeleteLoading.value = false;
        }
    });
};

// --- TABLE CONFIG ---
const columns: ColumnDef<Permission>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
];

const actionList: ActionItem<Permission>[] = [
    ...(canEdit.value ? [{
        label: 'Edit Permission',
        icon: Pencil,
        // Panggil Child
        onClick: (permission: Permission) => permissionFormRef.value?.openEditModal(permission),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete Permission',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (permission: Permission) => openDeleteAlert(permission),
    }] : []),
];

const breadcrumbs = [{ title: 'Permissions', href: '/permissions' }];
</script>

<template>
    <Head title="Permissions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            
            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Button 
                        class="w-32" 
                        @click="permissionFormRef?.openCreateModal()"
                    >
                        Create
                    </Button>
                </div>
            </div>

            <DataTable
                :data="props.permissions"
                :columns="columns"
                :actions="true"
                :action-list="actionList" 
            />
        </div>

        <PermissionForm ref="permissionFormRef" />

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete Permission?"
            :description="`Are you sure you want to delete the permission '${deleteForm.name}'? This cannot be undone.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>