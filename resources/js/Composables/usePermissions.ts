import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { AuthUser } from '@/Types/inertia.d';

export function usePermissions() {
    const page = usePage();

    const user = computed<AuthUser | null>(() => (page.props as any).auth?.user ?? null);
    const permissions = computed<string[]>(() => user.value?.permissions ?? []);
    const roles = computed<string[]>(() => user.value?.roles ?? []);
    const adminType = computed(() => user.value?.admin_type ?? null);

    function hasPermission(permission: string): boolean {
        const perms = permissions.value;
        if (perms.includes('*:*')) return true;
        if (perms.includes(permission)) return true;

        const [resource] = permission.split(':');
        if (perms.includes(`${resource}:*`)) return true;

        return false;
    }

    function hasAnyPermission(...permissionList: string[]): boolean {
        return permissionList.some((p) => hasPermission(p));
    }

    function hasRole(role: string): boolean {
        return roles.value.includes(role);
    }

    function isSuperAdmin(): boolean {
        return adminType.value === 'super_admin';
    }

    function isPlatformAdmin(): boolean {
        return adminType.value === 'platform_admin' || isSuperAdmin();
    }

    return {
        user,
        permissions,
        roles,
        adminType,
        hasPermission,
        hasAnyPermission,
        hasRole,
        isSuperAdmin,
        isPlatformAdmin,
    };
}
