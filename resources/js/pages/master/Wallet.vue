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
import WalletForm from '../../components/forms/WalletForm.vue';

interface Wallet {
    id: number
    name: string
    balance: number
}

const page = usePage();
const authPermissions = page.props.auth.permissions;

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('wallet-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('wallet-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('wallet-delete'));

const props = defineProps<{ wallets: Wallet[] }>();

// Ref ke Child Component
const walletFormRef = ref<InstanceType<typeof WalletForm> | null>(null);

// --- DELETE LOGIC (Di Parent) ---
const deleteForm = useForm({
    id: 0,
    name: '',
    balance: 0
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

    const openDeleteAlert = (wallet: Wallet) => {
    deleteForm.id = wallet.id;
    deleteForm.name = wallet.name;
    deleteForm.balance = wallet.balance;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('wallets.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Delete Wallet Success');
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
const columns: ColumnDef<Wallet>[] = [
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('name')),
    },
    {
        accessorKey: 'balance',
        header: 'Balance',
        cell: ({ row }) => h('div', { class: 'font-medium' }, `Rp. ${Number(row.getValue('balance')).toLocaleString('id-ID')}`),
    },
];

const actionList: ActionItem<Wallet>[] = [
    ...(canEdit.value ? [{
        label: 'Edit Wallet',
        icon: Pencil,
        // Panggil Child
        onClick: (wallet: Wallet) => walletFormRef.value?.openEditModal(wallet),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete Wallet',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (wallet: Wallet) => openDeleteAlert(wallet),
    }] : []),
];

const breadcrumbs = [{ title: 'Wallets', href: '/wallets' }];   
</script>

<template>
    <Head title="Wallets" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
          <div class="flex justify-between items-center gap-4">
            <div class="flex justify-between items-center gap-2">
                <div>
                    <h2 class="text-2xl font-bold">Wallets</h2>
                    <p class="text-sm text-gray-500">Manage your wallets</p>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Button 
                        class="w-32" 
                        @click="walletFormRef?.openCreateModal()"
                    >
                        Create
                    </Button>
                </div>
            </div>
          </div>          

            <DataTable
                :data="props.wallets"
                :columns="columns"
                :actions="true"
                :action-list="actionList" 
            />
        </div>

        <WalletForm ref="walletFormRef" />

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete Wallet?"
            :description="`Are you sure you want to delete the wallet '${deleteForm.name}'? This cannot be undone.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>