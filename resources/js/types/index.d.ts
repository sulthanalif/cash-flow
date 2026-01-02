import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
    roleName: string;
    permissions: string[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface FlashMessage {
    success?: string;
    error?: string;
    message?: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
    permission?: string | string[];
    items?: {
        title: string;
        href?: string;
        isActive?: boolean;
        permission?: string | string[];
    }[];
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> =
    T & {
        name: string;
        quote: { message: string; author: string };
        auth: Auth;
        sidebarOpen: boolean;
        flash: FlashMessage;
    };

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
