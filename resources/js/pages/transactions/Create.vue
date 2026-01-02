<script setup lang="ts">
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { route } from 'ziggy-js'; // Hapus jika sudah global
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import ComboBox from '@/components/ui/combo-box/ComboBox.vue';
import { computed, ref } from 'vue';
import InputSelect from '@/components/InputSelect.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'; // Opsional: Agar lebih cantik pakai Card
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput, InputGroupText, InputGroupTextarea } from '@/components/ui/input-group'
import Textarea from '@/components/ui/textarea/Textarea.vue';
import DatePicker from '@/components/DatePicker.vue';
import { toast } from 'vue-sonner';
import { parseDate, today, getLocalTimeZone } from '@internationalized/date'
import Spinner from '@/components/ui/spinner/Spinner.vue';

// 1. Interface Data
interface Category {
    id: number;
    name: string;
    type: string;
}

interface Wallet {
    id: number;
    name: string;
}

interface FormData {
    type: string;
    date: string;
    category_id: string;
    wallet_id: string;
    amount: string;
    description: string;
}

const isLoading = ref(false);

const props = defineProps<{
    categories: Category[];
    wallets: Wallet[];
}>();

// 2. Options untuk Select/Combo
const typeOptions = [
    { value: 'income', label: 'Income' },
    { value: 'expense', label: 'Expense' }
];

const categoryOptions = computed(() => {
    // 1. Ambil semua kategori dari props
    let categories = props.categories;

    // 2. Jika form.type sudah dipilih, filter kategori berdasarkan type tersebut
    if (form.type) {
        categories = categories.filter(category => category.type === form.type);
    }

    // 3. Mapping ke format yang diminta ComboBox
    return categories.map(category => ({
        value: String(category.id),
        label: category.name,
    }));
});

const walletOptions = computed(() => props.wallets.map(wallet => ({
    value: String(wallet.id),
    label: wallet.name,
})));

// 3. Setup Form
// Hamba menambahkan field 'type' agar bisa dipantau
const form = useForm<FormData>({
    type: '',
    date: today(getLocalTimeZone()) as any,
    category_id: '',
    wallet_id: '',
    amount: '',
    description: '',
});

// 4. Handle Perubahan Type
const onSelectType = (value: string) => {
    form.type = value;
    form.category_id = '';
}

// watch(() => form.type, (newVal) => {
//     // Reset category jika type berubah agar data bersih
//     form.category_id = '';
//     console.log('Type berubah menjadi:', newVal); // Debugging: Cek console browser
// });

const onSubmit = () => {
    isLoading.value = true;
    form.transform((data) => ({
        ...data,
        date: data.date.toString(),
    }));
    form.post(route('transactions.store'), {
        preserveScroll: true,
        onSuccess: () => {
            isLoading.value = false;
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
}

const breadcrumbs = [
    { title: 'Transactions', href: route('transactions.index') },
    { title: 'Create', href: route('transactions.create') },
];
</script>

<template>

    <Head title="Create Transaction" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4 md:p-8  mx-auto w-full">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Create Transaction</h2>
                    <p class="text-muted-foreground">Record your income or expenses.</p>
                </div>
            </div>

            <div class="border rounded-xl bg-card text-card-foreground shadow-sm">
                <div class="p-6 md:p-8">
                    <form @submit.prevent="onSubmit" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <Label class="text-base font-semibold">Date</Label>
                                <DatePicker
                                    v-model="form.date"
                                    placeholder="Select date..."
                                    required
                                />
                                <InputError :message="form.errors.date" />
                            </div>
                            <div class="space-y-2">
                                <Label class="text-base font-semibold">Type</Label>
                                <InputSelect label="Select Type" placeholder="Select transaction type..."
                                    :options="typeOptions" @select="onSelectType" v-model="form.type" required />
                                <InputError :message="form.errors.type" />
                            </div>

                            <div v-if="form.type" class="space-y-2 animate-in fade-in slide-in-from-top-2 duration-300">
                                <Label class="text-base font-semibold">Category</Label>
                                <ComboBox v-model="form.category_id" :options="categoryOptions"
                                    placeholder="Select category..." search-placeholder="Search category..."
                                    empty-text="No category found." required />
                                <InputError :message="form.errors.category_id" />
                            </div>
                        </div>

                        <!-- <div class="border-t border-dashed"></div> -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label class="text-base font-semibold">Wallet</Label>
                                <ComboBox v-model="form.wallet_id" :options="walletOptions"
                                    placeholder="Select wallet..." search-placeholder="Search wallet..."
                                    empty-text="No wallet found." required />
                                <InputError :message="form.errors.wallet_id" />
                            </div>

                            <div class="space-y-2">
                                <Label class="text-base font-semibold">Amount</Label>
                                <InputGroup>
                                    <InputGroupAddon>
                                        <InputGroupText>Rp.</InputGroupText>
                                    </InputGroupAddon>
                                    <InputGroupInput  v-model="form.amount" type="number" class="!pl-1" required />
                                </InputGroup>
                                <InputError :message="form.errors.amount" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="description" class="text-base font-semibold">Description</Label>
                            <Textarea id="description" placeholder="e.g. Lunch at Padang Restaurant"
                                v-model="form.description" />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="flex justify-end pt-4">
                            <Button type="submit" size="lg" :disabled="form.processing" class="w-full md:w-auto">
                                <Spinner v-if="isLoading" class="animate-spin" />
                                <span>Save Transaction</span>
                            </Button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
