<script setup lang="ts">
import { h, ref, computed, watch } from 'vue';
import { Head, usePage, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable, { type ActionItem } from '@/components/ui/data-table/DataTable.vue';
import { type ColumnDef } from '@tanstack/vue-table';
import { Banknote, Calendar1Icon, Eye, HashIcon, NotepadText, Pencil, TagIcon, Trash, Wallet2Icon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/AlertDialog.vue';
import { route } from 'ziggy-js';
import Modal from '@/components/Modal.vue';


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

const canCreate = computed(() => Array.isArray(authPermissions) && authPermissions.includes('transaction-create'));
const canEdit = computed(() => Array.isArray(authPermissions) && authPermissions.includes('transaction-edit'));
const canDelete = computed(() => Array.isArray(authPermissions) && authPermissions.includes('transaction-delete'));

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
const isDetailOpen = ref(false);

const detailTransaction = ref<Transaction | null>(null);

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (date: string) => {
    const d = new Date(date);
    const months = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
}

const openDetailModal = (transaction: Transaction) => {
    detailTransaction.value = transaction;
    isDetailOpen.value = true;
};

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
        label: 'Detail Transaction',
        icon: Eye,
        onClick: (transaction: Transaction) => openDetailModal(transaction),
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

        <Modal
            v-model:open="isDetailOpen"
            title="Transaction Details"
            description="View the details of the selected transaction"
        >
            <div v-if="detailTransaction" class="space-y-6">
                <div class="grid grid-cols-2 gap-y-6 gap-x-4">

                    <div class="space-y-1">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <Calendar1Icon class="w-4 h-4 mr-1.5" />
                            Date
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ formatDate(detailTransaction.date) }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <HashIcon class="w-4 h-4 mr-1.5" />
                            Transaction Code
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ detailTransaction.code }}
                        </p>

                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <TagIcon class="w-4 h-4 mr-1.5" />
                            Category
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ detailTransaction.category.type +' - '+ detailTransaction.category.name }}
                        </p>
                    </div>

                    <div class="space-y-1 ">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <Wallet2Icon class="w-4 h-4 mr-1.5" />
                            Wallet
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ detailTransaction.wallet.name }}
                        </p>
                    </div>

                    <div class="space-y-1 ">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <Banknote class="w-4 h-4 mr-1.5" />
                            Amount
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ formatCurrency(detailTransaction.amount) }}
                        </p>
                    </div>

                    <div class="space-y-1 ">
                        <div class="flex items-center text-sm text-gray-500 mb-1">
                            <NotepadText class="w-4 h-4 mr-1.5" />
                            Description
                        </div>
                        <p class="font-medium ml-5.5">
                            {{ detailTransaction.description }}
                        </p>
                    </div>
                </div>
            </div>

        </Modal>

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
