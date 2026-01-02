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
import InputSelect from '../InputSelect.vue';

// Interface Data
interface Category {
    id: number;
    name: string;
    type: string;
    description: string;
}

// Interface Form
interface CategoryForm {
    id: number;
    user_id: number;
    name: string;
    type: string;
    description: string;
}

const auth = usePage().props.auth;
// State Form
const form = useForm<CategoryForm>({
    id: 0,
    user_id: auth.user.id,
    name: '',
    type: '',
    description: '',
});

// Category Types
const categoryTypes = [
    { value: 'expense', label: 'Expense' },
    { value: 'income', label: 'Income' },
];



const isSheetOpen = ref(false);
const titleSheet = ref('');
const descriptionSheet = ref('');
const isLoading = ref(false);

const onSubmit = () => {
    isLoading.value = true;

    const url = form.id
        ? route('categories.update', form.id)
        : route('categories.store');
    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(
                form.id
                    ? 'Update Category Success'
                    : 'Create Category Success',
            );
            form.reset();
            form.clearErrors();
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
    titleSheet.value = 'Create Category';
    descriptionSheet.value = 'Add a new category to the system.';
    isSheetOpen.value = true;
};

const openEditModal = (category: Category) => {
    form.clearErrors();
    form.id = category.id;
    form.name = category.name;
    form.type = category.type;
    form.description = category.description;
    titleSheet.value = 'Edit Category';
    descriptionSheet.value = 'Modify the category details.';
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
                    id="categoryForm"
                    @submit.prevent="onSubmit"
                    class="flex flex-col gap-6"
                >
                    <div class="grid gap-2">
                        <Label for="name" class="font-semibold"
                            >Category Name</Label
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
                        <Label for="type" class="font-semibold"
                            >Category Type</Label
                        >
                        <InputSelect
                            id="type"
                            type="text"
                            required
                            v-model="form.type"
                            :options="categoryTypes"
                            label="Category Type"
                            placeholder="Select category type"
                        />
                        <InputError :message="form.errors.type" />
                        <p class="text-[0.8rem] text-muted-foreground">
                            Use a convention like <code>expense</code> or
                            <code>income</code>.
                        </p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="description" class="font-semibold"
                            >Category Description</Label
                        >
                        <Input
                            id="description"
                            type="text"
                            placeholder="e.g. Expense category"
                            v-model="form.description"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                </form>
            </div>

            <SheetFooter
                class="mt-auto  px-6 py-4 sm:justify-between dark:bg-transparent"
            >
                <Button
                    type="submit"
                    form="categoryForm"
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
