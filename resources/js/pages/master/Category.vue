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
import CategoryForm from '../../components/forms/CategoryForm.vue';

interface Category {
    id: number
    name: string
    type: string
    description: string
}

const page = usePage();
const authPermissions = page.props.auth.permissions;

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('category-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('category-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('category-delete'));

const props = defineProps<{ categories: Category[] }>();

// Ref ke Child Component
const categoryFormRef = ref<InstanceType<typeof CategoryForm> | null>(null);

// --- DELETE LOGIC (Di Parent) ---
const deleteForm = useForm({
    id: 0,
    name: '',
    type: '',
    description: ''
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

const openDeleteAlert = (category: Category) => {
    deleteForm.id = category.id;
    deleteForm.name = category.name;
    deleteForm.type = category.type;
    deleteForm.description = category.description;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('categories.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Delete Category Success');
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
const columns: ColumnDef<Category>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
        accessorKey: 'type',
        header: 'Type',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('type')),
    },
    {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('description') ?? '-'),
    },
];

const actionList: ActionItem<Category>[] = [
    ...(canEdit.value ? [{
        label: 'Edit Category',
        icon: Pencil,
        // Panggil Child
        onClick: (category: Category) => categoryFormRef.value?.openEditModal(category),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete Category',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (category: Category) => openDeleteAlert(category),
    }] : []),
];

const breadcrumbs = [{ title: 'Categories', href: '/categories' }];
</script>

<template>
    <Head title="Categories" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
          <div class="flex justify-between items-center gap-4">
            <div class="flex justify-between items-center gap-2">
                <div>
                    <h2 class="text-2xl font-bold">Categories</h2>
                    <p class="text-sm text-gray-500">Manage your categories</p>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Button 
                        class="w-32" 
                        @click="categoryFormRef?.openCreateModal()"
                    >
                        Create
                    </Button>
                </div>
            </div>
          </div>          

            <DataTable
                :data="props.categories"
                :columns="columns"
                :actions="true"
                :action-list="actionList" 
            />
        </div>

        <CategoryForm ref="categoryFormRef" />

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete Category?"
            :description="`Are you sure you want to delete the category '${deleteForm.name}'? This cannot be undone.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>