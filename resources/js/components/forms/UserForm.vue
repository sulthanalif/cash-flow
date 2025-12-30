<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from '@/components/ui/sheet';
import { Button } from '@/components/ui/button';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import InputError from '@/components/InputError.vue';
import ComboBox from '@/components/ui/combo-box/ComboBox.vue';
import Spinner from '../ui/spinner/Spinner.vue';

// --- Props & Interfaces ---
const props = defineProps<{
    roleOptions: { value: string; label: string }[]
}>();

interface User {
    id: number;
    name: string;
    email: string;
    roles: { id: number; name: string }[];
}

interface UserForm {
    id: number;
    name: string;
    email: string;
    password: string;
    role: string;
}

// --- State ---
const form = useForm<UserForm>({
    id: 0,
    name: '',
    email: '',
    password: '',
    role: ''
});

const isSheetOpen = ref(false);
const titleSheet = ref('');
const descriptionSheet = ref('');
const isLoading = ref(false);

// --- Logic ---
const onSubmit = () => {
    isLoading.value = true;
    const url = form.id ? route('users.update', form.id) : route('users.store');
    const method = form.id ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success(form.id ? 'Update User Success' : 'Create User Success');
            closeSheet();
        },
        onError: (errors: any) => {
            if (errors.message && !Object.keys(errors).some(k => k !== 'message')) {
                toast.error(errors.message);
            }
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const closeSheet = () => {
    isSheetOpen.value = false;
    setTimeout(() => {
        form.reset();
        form.clearErrors();
    }, 300);
}

// --- Exposed Methods ---
const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.id = 0; 
    titleSheet.value = 'Create New User';
    descriptionSheet.value = 'Add a new user to the system and assign a role.';
    isSheetOpen.value = true;
};

const openEditModal = (user: User) => {
    form.clearErrors();
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.password = ''; // Password dikosongkan saat edit (biasanya opsional di backend)
    // Ambil role pertama jika ada
    form.role = user.roles.length > 0 ? user.roles[0].name : '';
    
    titleSheet.value = 'Edit User';
    descriptionSheet.value = 'Update user details and access level.';
    isSheetOpen.value = true;
};

defineExpose({
    openCreateModal,
    openEditModal
});
</script>

<template>
    <Sheet v-model:open="isSheetOpen">
        <SheetContent class="w-full sm:max-w-md h-full flex flex-col p-0 gap-0">
            
            <SheetHeader class="px-6 py-6 border-b">
                <SheetTitle>{{ titleSheet }}</SheetTitle>
                <SheetDescription>
                    {{ descriptionSheet }}
                </SheetDescription>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-6">
                <form id="userForm" @submit.prevent="onSubmit" class="flex flex-col gap-6">
                    
                    <div class="grid gap-2">
                        <Label for="name" class="font-semibold">Full Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            placeholder="e.g. John Doe"
                            v-model="form.name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email" class="font-semibold">Email Address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            placeholder="john@example.com"
                            v-model="form.email"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="roles" class="font-semibold">Role</Label>
                        <ComboBox
                            v-model="form.role"
                            :options="props.roleOptions"
                            placeholder="Select a role..."
                            search-placeholder="Search role..."
                            empty-text="No role found."
                        />
                        <InputError :message="form.errors.role" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password" class="font-semibold">
                            Password 
                            <span v-if="form.id" class="text-xs font-normal text-muted-foreground ml-1">(Leave blank to keep current)</span>
                        </Label>
                        <Input
                            id="password"
                            type="password"
                            :required="!form.id" 
                            placeholder="••••••••"
                            v-model="form.password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                </form>
            </div>

            <SheetFooter
                class="mt-auto px-6 py-4 sm:justify-between dark:bg-transparent"
            >
                <Button type="submit" form="userForm" :disabled="isLoading">
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