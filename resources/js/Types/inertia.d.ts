import type { AdminType } from './enums';

export interface AuthUser {
    id: number;
    name: string;
    email: string;
    admin_type: AdminType;
    permissions: string[];
    roles: string[];
}

export interface PageProps {
    auth: {
        user: AuthUser | null;
    };
    flash: {
        success: string | null;
        error: string | null;
    };
    locale: string;
    [key: string]: unknown;
}

declare module '@inertiajs/vue3' {
    interface PageProps extends PageProps {}
}

declare module 'ziggy-js' {
    export function route(name: string, params?: Record<string, unknown>, absolute?: boolean): string;
    export function route(): { current: (name: string) => boolean };
}

declare global {
    interface Window {
        route: typeof import('ziggy-js').route;
    }
}
