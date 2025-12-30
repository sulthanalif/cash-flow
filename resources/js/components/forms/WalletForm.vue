<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import Spinner from '../ui/spinner/Spinner.vue';

// Interface Data
interface Wallet {
    id: number;
    name: string;
    balance: number;
}

// Interface Form
interface WalletForm {
    id: number;
    user_id: number;
    name: string;
    balance: number;
}

const auth = usePage().props.auth;
// State Form
const form = useForm<WalletForm>({
    id: 0,
    user_id: auth.user.id,
    name: '',
    balance: 0,
}); 

const isSheetOpen = ref(false);
const titleSheet = ref('');
const descriptionSheet = ref('');
const isLoading = ref(false);

const onSubmit = () => {
    isLoading.value = true;

    console.log(form);

    const url = form.id
        ? route('wallets.update', form.id)
        : route('wallets.store');
    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                form.id
                    ? 'Update Wallet Success'
                    : 'Create Wallet Success',
            );
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

// --- Exposed Methods ---

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.id = 0;
    titleSheet.value = 'Create Wallet';
    descriptionSheet.value = 'Add a new wallet to the system.';
    isSheetOpen.value = true;
};

const openEditModal = (wallet: Wallet) => {
    form.clearErrors();
    form.id = wallet.id;
    form.name = wallet.name;
    form.balance = wallet.balance;
    titleSheet.value = 'Edit Wallet';
    descriptionSheet.value = 'Modify the wallet details.';
    isSheetOpen.value = true;
};

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
                    id="walletForm"
                    @submit.prevent="onSubmit"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name" class="font-semibold"
                            >Wallet Name</Label
                        >
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            placeholder="e.g. category-name"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" />
                        <p class="text-[0.8rem] text-muted-foreground">
                            Use a convention like <code>resource-action</code>
                            (e.g., category-expense).
                        </p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="balance" class="font-semibold"
                            >Wallet Balance</Label
                        >
                        <Input
                            id="balance"
                            type="number"
                            required
                            placeholder="e.g. 1000000"
                            v-model="form.balance"
                        />
                        <InputError :message="form.errors.balance" />
                    </div>
                </form>
            </div>

            <SheetFooter
                class="mt-auto  px-6 py-4 sm:justify-between dark:bg-transparent"
            >
                <Button
                    type="submit"
                    form="walletForm"
                    :disabled="isLoading"
                >
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
