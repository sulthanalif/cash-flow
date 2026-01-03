<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3'; // Jangan lupa import Link
import CardStat from '@/components/CardStat.vue';
import { computed } from 'vue';
import { TrendingUp, TrendingDown, Wallet, Activity, CreditCard } from 'lucide-vue-next'; // Tambah CreditCard
import Table from '@/components/Table.vue';

const props = defineProps({
    incomeMonth: { type: Number, default: 0 },
    expenseMonth: { type: Number, default: 0 },
    wallets: {
        type: Array as () => Array<{ id: number, name: string, balance: number }>,
        default: () => [],
    },
    recentTransactions: { type: Array, default: () => [] }
})

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
];

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
    }).format(value);
};

// Mengubah class amount agar rata kanan visualnya pas
const transactionColumns = [
    { key: 'date', label: 'Date', class: 'w-[100px] md:w-[120px]' },
    { key: 'category', label: 'Category' },
    { key: 'wallet', label: 'Wallet', class: 'hidden md:table-cell' }, // Sembunyikan wallet di HP agar tidak sempit
    { key: 'amount', label: 'Amount', class: 'text-right', cellClass: 'text-right font-medium' },
];

const balance = computed(() => props.incomeMonth - props.expenseMonth);
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-4 md:p-8">

            <div>
                <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
                <p class="text-muted-foreground">Monthly financial overview.</p>
            </div>

            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <CardStat
                    title="Total Income"
                    :value="formatCurrency(incomeMonth)"
                    :icon="TrendingUp"
                    description="Bulan ini"
                    trend="up"
                />
                <CardStat
                    title="Total Expense"
                    :value="formatCurrency(expenseMonth)"
                    :icon="TrendingDown"
                    description="Bulan ini"
                    trend="down"
                />
                <div class="sm:col-span-2 lg:col-span-1">
                    <CardStat
                        title="Net Cashflow"
                        :value="formatCurrency(balance)"
                        :icon="Wallet"
                        description="Net Cashflow"
                        :trend="balance >= 0 ? 'up' : 'down'"
                    />
                </div>
            </div>

            <div class="grid gap-4 grid-cols-1 lg:grid-cols-7 items-stretch">

                <div class="col-span-1 lg:col-span-4 h-full flex flex-col rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 flex-1">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <Wallet class="h-5 w-5 text-primary" />
                                <h3 class="font-semibold leading-none tracking-tight">My Wallets</h3>
                            </div>
                        </div>

                        <div v-if="wallets.length > 0" class="grid gap-4 grid-cols-1 sm:grid-cols-2">
                            <div
                                v-for="(wallet, index) in wallets"
                                :key="index"
                                class="relative overflow-hidden rounded-xl border p-5 transition-all hover:shadow-md group bg-gradient-to-br from-slate-900 to-slate-800 text-white"
                            >
                                <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-white/5 blur-xl group-hover:bg-white/10 transition"></div>
                                <div class="relative z-10 flex flex-col justify-between h-full gap-4">
                                    <div class="flex justify-between items-start">
                                        <p class="font-bold text-lg tracking-tight">{{ wallet.name }}</p>
                                        <CreditCard class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-300 mb-1">Current Balance</p>
                                        <p class="text-xl font-bold truncate">{{ formatCurrency(wallet.balance) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="h-full min-h-[200px] flex flex-col items-center justify-center text-center border-2 border-dashed rounded-xl bg-gray-50/50">
                            <div class="p-3 bg-white rounded-full shadow-sm mb-3">
                                <Wallet class="h-6 w-6 text-gray-400" />
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">No Wallets Found</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-xs">
                                You haven't created any wallets yet.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 lg:col-span-3 h-full flex flex-col rounded-xl border bg-card text-card-foreground shadow">
                    <div class="p-6 flex-1 flex flex-col overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold leading-none tracking-tight">Recent Transactions</h3>
                            <Activity class="h-4 w-4 text-muted-foreground" />
                        </div>

                        <div class="flex-1 overflow-auto -mx-2 px-2">
                            <Table
                                :columns="transactionColumns"
                                :data="recentTransactions"
                                empty-message="No recent transactions found."
                            >
                                <template #date="{ row }">
                                    <span class="text-xs text-muted-foreground whitespace-nowrap">{{ row.date }}</span>
                                </template>

                                <template #category="{ row }">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-sm truncate max-w-[100px]">{{ row.category?.name }}</span>
                                        <span class="text-[10px] text-muted-foreground uppercase">{{ row.category?.type }}</span>
                                    </div>
                                </template>

                                <template #wallet="{ row }">
                                    <span class="text-xs text-muted-foreground">{{ row.wallet?.name }}</span>
                                </template>

                                <template #amount="{ row }">
                                    <span :class="row.category?.type === 'income' ? 'text-green-600' : 'text-red-600'" class="whitespace-nowrap font-semibold text-sm">
                                        {{ row.category?.type === 'income' ? '+' : '-' }}
                                        {{ formatCurrency(row.amount) }}
                                    </span>
                                </template>
                            </Table>
                        </div>
                    </div>

                    <div class="p-4 border-t bg-muted/50 text-center mt-auto">
                        <Link :href="route('transactions.index')" class="text-xs hover:underline font-medium">
                            View All Transactions &rarr;
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
