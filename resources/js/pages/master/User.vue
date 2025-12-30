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

// Import Child Form
import UserForm from '../../components/forms/UserForm.vue';

// Interfaces
interface User {
    id: number;
    name: string;
    email: string;
    roles: { id: number; name: string }[];
}

interface Role {
    id: number;
    name: string;
}

// Page Props & Permissions
const page = usePage();
const authPermissions = page.props.auth.permissions;

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-delete'));

const props = defineProps<{ users: User[], roles: Role[] }>();

// Prepare Role Options for Child Component
const rolesOptions = computed(() => props.roles.map(role => ({
    value: role.name,
    label: role.name
})));

// Ref to Child Component
const userFormRef = ref<InstanceType<typeof UserForm> | null>(null);

// --- DELETE LOGIC (Parent) ---
const deleteForm = useForm({
    id: 0,
    name: ''
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

const openDeleteAlert = (user: User) => {
    deleteForm.id = user.id;
    deleteForm.name = user.name;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('users.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('User deleted successfully');
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
const columns: ColumnDef<User>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
        accessorKey: 'email',
        header: 'Email',
        cell: ({ row }) => h('div', row.getValue('email')),
    },
    {
        accessorKey: 'roles',
        header: 'Roles',
        cell: ({ row }) => {
            const roles = row.original.roles;
            return h('div', { class: 'flex flex-wrap gap-1' },
                roles.map(role => h(Badge, { variant: 'outline' }, () => role.name))
            );
        },
    }
];

const actionList: ActionItem<User>[] = [
    ...(canEdit.value ? [{
        label: 'Edit User',
        icon: Pencil,
        onClick: (user: User) => userFormRef.value?.openEditModal(user),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete User',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (user: User) => openDeleteAlert(user),
    }] : []),
];

const breadcrumbs = [{ title: 'Users', href: '/users' }];
</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">

            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Button 
                        class="w-32" 
                        @click="userFormRef?.openCreateModal()"
                    >
                        Create
                    </Button>
                </div>
            </div>

            <DataTable
                :data="props.users"
                :columns="columns"
                :actions="true"
                :action-list="actionList" 
            />

        </div>

        <UserForm 
            ref="userFormRef" 
            :role-options="rolesOptions" 
        />

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete User?"
            :description="`Are you sure you want to delete user ${deleteForm.name}? This action cannot be undone.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>