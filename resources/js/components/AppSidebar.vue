<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, ShieldCheck, Wallet } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

const userPermissions = computed(() => {
    return (page.props.auth as any).permissions || [];
});

const can = (permission: string | string[]) => {
    if (Array.isArray(permission)) {
        return permission.some(p => userPermissions.value.includes(p));
    }

    return userPermissions.value.includes(permission);
}

interface NavItemWithPermission extends NavItem {
    permission?: string | string[];
}

const rawMainNavItems: NavItemWithPermission[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutGrid,
        isActive: route().current('dashboard'),
        permission: 'dashboard',
    },
    {
        title: 'Categories',
        href: route('categories.index'),
        icon: Folder,
        isActive: route().current('categories.*'),
        permission: 'category-page',
    },
    {
        title: 'Wallets',
        href: route('wallets.index'),
        icon: Wallet,
        isActive: route().current('wallets.*'),
        permission: 'wallet-page',
    },
    {
        title: 'Transactions',
        href: route('transactions.index'),
        icon: BookOpen,
        isActive: route().current('transactions.*'),
        // permission: 'transaction-page',
    },
    {
        title: 'Authentication',
        href: '#',
        icon: ShieldCheck,
        isActive: route().current('users.*') || route().current('roles.*') || route().current('permissions.*'),
        permission: ['user-page', 'role-page', 'permission-page'],
        items: [
            {
                title: 'Users',
                href: route('users.index'),
                isActive: route().current('users.*'),
                permission: 'user-page',
            },
            {
                title: 'Roles',
                href: route('roles.index'),
                isActive: route().current('roles.*'),
                permission: 'role-page',
            },
            {
                title: 'Permissions',
                href: route('permissions.index'),
                isActive: route().current('permissions.*'),
                permission: 'permission-page',
            },
        ]
    },
];

const mainNavItems = computed(() => {
    return rawMainNavItems.map(item => {
        if (item.items) {
            const filteredChildren = item.items.filter(child => {
                return !child.permission || can(child.permission);
            });

            return {
                ...item,
                items: filteredChildren
            };
        }

        return item;
    }).filter(item => {

        if (item.permission && !can(item.permission)) {
            return false;
        }

        if (item.items) {
            return item.items.length > 0;
        }

        return true;
    });
});

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
