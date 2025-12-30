<script setup lang="ts">
import { h, ref, computed } from 'vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable, { type ActionItem } from '@/components/ui/data-table/DataTable.vue';
import { type ColumnDef } from '@tanstack/vue-table';
import { Pencil, Trash } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/AlertDialog.vue';
// Import Form Component yang baru kita buat
import RoleForm from '../../components/forms/RoleForm.vue'; 

// Interfaces
interface Permission {
    id: number
    name: string
    guard_name: string
}
interface Role {
    id: number
    name: string
    permissions: { id: number; name: string }[]
}

// Props & Page Data
const props = defineProps<{ roles: Role[], permissions: Permission[] }>();
const page = usePage();
const authPermissions = page.props.auth.permissions;

// Permission Checks
const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('role-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('role-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('role-delete'));

// --- LOGIKA FORM CREATE/EDIT (Menggunakan Ref ke Child) ---
const roleFormRef = ref<InstanceType<typeof RoleForm> | null>(null);

const permissionsOptions = computed(() => props.permissions.map(permission => ({
    value: permission.name,
    label: permission.name
})));

// --- LOGIKA DELETE (Tetap di Parent) ---
// Kita buat form khusus delete agar terpisah dari form create/edit
const deleteForm = useForm({
    id: 0,
    name: '' // Hanya untuk display di dialog
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

const openDeleteAlert = (role: Role) => {
    deleteForm.id = role.id;
    deleteForm.name = role.name;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('roles.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Delete Role Success');
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
const columns: ColumnDef<Role>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', row.getValue('name')),
    },
    {
        accessorKey: 'permissions',
        header: 'Permissions',
        cell: ({ row }) => {
            const permissions = row.original.permissions;
            return h('div', { class: 'flex flex-wrap gap-1' },
                permissions.map((permission, idx) => {
                    if (idx < 5) return h(Badge, { variant: 'outline' }, () => permission.name);
                    if (idx === 5) return h('span', { class: 'text-xs text-gray-500' }, `+${permissions.length - 5} more`);
                    return null;
                }).filter(Boolean)
            );
        },
    }
];

const actionList: ActionItem<Role>[] = [
    ...(canEdit.value ? [{
        label: 'Edit Role',
        icon: Pencil,
        // Panggil fungsi milik Child Component
        onClick: (role: Role) => roleFormRef.value?.openEditModal(role),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete Role',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (role: Role) => openDeleteAlert(role),
    }] : []),
];

const breadcrumbs = [{ title: 'Roles', href: '/roles' }];
</script>

<template>
    <Head title="Roles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Button 
                        class="w-32" 
                        @click="roleFormRef?.openCreateModal()"
                    >
                        Create
                    </Button>
                </div>
            </div>

            <DataTable
                :data="props.roles"
                :columns="columns"
                :actions="true"
                :action-list="actionList" 
            />
        </div>

        <RoleForm 
            ref="roleFormRef" 
            :permission-options="permissionsOptions" 
        />

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete Role?"
            :description="`Are you sure you want to delete the role named ${deleteForm.name}? This data cannot be restored.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>