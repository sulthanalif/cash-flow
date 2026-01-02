<script setup lang="ts">
import { h, ref, computed, watch } from 'vue';
import { Head, usePage, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable, { type ActionItem } from '@/components/ui/data-table/DataTable.vue';
import { type ColumnDef } from '@tanstack/vue-table';
import { Pencil, Trash } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/AlertDialog.vue';
import { route } from 'ziggy-js';


// Import Child Form
// import TransactionForm from '../../components/forms/TransactionForm.vue';

// Interfaces
interface Transaction {
    id: number;
    code: string;
    user_id: number;
    date: string;
    category: {
        type: string;
        name: string;
    };
    wallet: {
        name: string;
    };
    amount: number;
    description: string;
}

// Page Props & Permissions
const page = usePage();
const authPermissions = page.props.auth.permissions;

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('user-delete'));

const props = defineProps<{ transactions: Transaction[] }>();


// Ref to Child Component
// const transactionFormRef = ref<InstanceType<typeof TransactionForm> | null>(null);

// --- DELETE LOGIC (Parent) ---
const deleteForm = useForm({
    id: 0,
    code: ''
});
const isDeleteOpen = ref(false);
const isDeleteLoading = ref(false);

const openDeleteAlert = (transaction: Transaction) => {
    deleteForm.id = transaction.id;
    deleteForm.code = transaction.code;
    isDeleteOpen.value = true;
};

const onDelete = () => {
    isDeleteLoading.value = true;
    deleteForm.delete(route('transactions.destroy', deleteForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Transaction deleted successfully');
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
const columns: ColumnDef<Transaction>[] = [
    {
        accessorKey: 'code',
        header: 'Code',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('code')),
    },
    {
        accessorKey: 'date',
        header: 'Date',
        cell: ({ row }) => {
            const raw = row.getValue('date') as string;
            // e.g. 2025-06-25 -> 25 Juni 2025
            const d = new Date(raw);
            const months = [
                'Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'
            ];
            return h('div', `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`);
        },
    },
    {
        accessorKey: 'wallet',
        header: 'Wallet',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.original.wallet?.name ?? '-'),
    },
    {
        accessorKey: 'category',
        header: 'Type/Category',
        cell: ({ row }) => {
            const category = row.original.category;

            if (!category) return h('span', { class: 'text-muted-foreground' }, '-');

            const isIncome = category.type === 'income';

            return h('div', { class: 'flex flex-wrap gap-2 items-center' }, [
                // Badge Type: Pakai class conditional agar berwarna
                h(Badge, {
                    variant: isIncome ? 'default' : 'destructive', // default=hitam/biru, destructive=merah
                    class: isIncome ? 'bg-green-600 hover:bg-green-700' : '' // Paksa hijau jika income
                }, () => isIncome ? 'Income' : 'Expense'),

                // Badge Name: Gunakan variant outline
                h(Badge, { variant: 'outline' }, () => category.name)
            ]);
        },
    },
    {
        accessorKey: 'amount',
        header: 'Amount',
        cell: ({ row }) => {
            const value = Number(row.getValue('amount'));
            return h('div', new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value));
        },
    },
    {
        accessorKey: 'description',
        header: 'Description',
        cell: ({ row }) => h('div', row.original.description ?? '-'),
    },
];

const actionList: ActionItem<Transaction>[] = [
    ...(canEdit.value ? [{
        label: 'Edit Transaction',
        icon: Pencil,
        onClick: (transaction: Transaction) => console.log('Edit Transaction', transaction),
    }] : []),
    ...(canDelete.value ? [{
        label: 'Delete Transaction',
        icon: Trash,
        class: 'text-red-600 focus:text-red-600',
        onClick: (transaction: Transaction) => openDeleteAlert(transaction),
    }] : []),
];

const breadcrumbs = [{ title: 'Transactions', href: '/transactions' }];
</script>

<template>
    <Head title="Transactions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">

            <div class="flex justify-between items-center gap-4">
            <div class="flex justify-between items-center gap-2">
                <div>
                    <h2 class="text-2xl font-bold">Transactions</h2>
                    <p class="text-sm text-gray-500">Manage your transactions</p>
                </div>
            </div>
            <div class="flex justify-end items-center gap-2">
                <div v-if="canCreate">
                    <Link :href="route('transactions.create')">
                        <Button
                            class="w-32"
                        >
                            Create
                        </Button>
                    </Link>
                </div>
            </div>
          </div>

            <DataTable
                :data="props.transactions"
                :columns="columns"
                :actions="true"
                :action-list="actionList"
            />

        </div>

        <!-- <TransactionForm
            ref="transactionFormRef"
        /> -->

        <ConfirmDialog
            v-model:open="isDeleteOpen"
            title="Delete Transaction?"
            :description="`Are you sure you want to delete transaction ${deleteForm.code}? This action cannot be undone.`"
            confirm-text="Yes, Delete"
            cancel-text="Cancel"
            variant="destructive"
            :is-loading="isDeleteLoading"
            @confirm="onDelete"
        />
    </AppLayout>
</template>
