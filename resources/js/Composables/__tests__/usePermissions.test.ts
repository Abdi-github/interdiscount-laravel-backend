import { describe, it, expect, vi, beforeEach } from 'vitest';

// Mock @inertiajs/vue3 usePage
const mockPageProps = { auth: { user: null as any } };

vi.mock('@inertiajs/vue3', () => ({
    usePage: () => ({ props: mockPageProps }),
}));

import { usePermissions } from '@/Composables/usePermissions';

describe('usePermissions', () => {
    beforeEach(() => {
        mockPageProps.auth.user = null;
    });

    describe('when user is null', () => {
        it('returns null user', () => {
            const { user } = usePermissions();
            expect(user.value).toBeNull();
        });

        it('returns empty permissions', () => {
            const { permissions } = usePermissions();
            expect(permissions.value).toEqual([]);
        });

        it('returns empty roles', () => {
            const { roles } = usePermissions();
            expect(roles.value).toEqual([]);
        });

        it('hasPermission returns false', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('products:read')).toBe(false);
        });

        it('isSuperAdmin returns false', () => {
            const { isSuperAdmin } = usePermissions();
            expect(isSuperAdmin()).toBe(false);
        });
    });

    describe('when user is super_admin', () => {
        beforeEach(() => {
            mockPageProps.auth.user = {
                id: 1,
                name: 'Hans Müller',
                email: 'admin@test.ch',
                admin_type: 'super_admin',
                permissions: ['*:*'],
                roles: ['super_admin'],
            };
        });

        it('isSuperAdmin returns true', () => {
            const { isSuperAdmin } = usePermissions();
            expect(isSuperAdmin()).toBe(true);
        });

        it('isPlatformAdmin returns true (super_admin implies platform_admin)', () => {
            const { isPlatformAdmin } = usePermissions();
            expect(isPlatformAdmin()).toBe(true);
        });

        it('hasPermission returns true for any permission via wildcard', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('products:read')).toBe(true);
            expect(hasPermission('orders:update')).toBe(true);
            expect(hasPermission('users:delete')).toBe(true);
        });

        it('hasAnyPermission returns true', () => {
            const { hasAnyPermission } = usePermissions();
            expect(hasAnyPermission('products:read', 'orders:read')).toBe(true);
        });

        it('hasRole returns true for super_admin', () => {
            const { hasRole } = usePermissions();
            expect(hasRole('super_admin')).toBe(true);
        });

        it('hasRole returns false for non-assigned role', () => {
            const { hasRole } = usePermissions();
            expect(hasRole('store_manager')).toBe(false);
        });
    });

    describe('when user is platform_admin', () => {
        beforeEach(() => {
            mockPageProps.auth.user = {
                id: 2,
                name: 'Admin EN',
                email: 'admin.en@test.ch',
                admin_type: 'platform_admin',
                permissions: ['products:*', 'orders:read', 'orders:update'],
                roles: ['platform_admin'],
            };
        });

        it('isSuperAdmin returns false', () => {
            const { isSuperAdmin } = usePermissions();
            expect(isSuperAdmin()).toBe(false);
        });

        it('isPlatformAdmin returns true', () => {
            const { isPlatformAdmin } = usePermissions();
            expect(isPlatformAdmin()).toBe(true);
        });

        it('hasPermission returns true for resource wildcard', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('products:read')).toBe(true);
            expect(hasPermission('products:create')).toBe(true);
            expect(hasPermission('products:delete')).toBe(true);
        });

        it('hasPermission returns true for direct match', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('orders:read')).toBe(true);
            expect(hasPermission('orders:update')).toBe(true);
        });

        it('hasPermission returns false for non-granted permission', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('users:read')).toBe(false);
            expect(hasPermission('orders:delete')).toBe(false);
        });

        it('hasAnyPermission returns true if at least one matches', () => {
            const { hasAnyPermission } = usePermissions();
            expect(hasAnyPermission('users:read', 'products:read')).toBe(true);
        });

        it('hasAnyPermission returns false if none match', () => {
            const { hasAnyPermission } = usePermissions();
            expect(hasAnyPermission('users:read', 'users:delete')).toBe(false);
        });
    });

    describe('when user is store_manager', () => {
        beforeEach(() => {
            mockPageProps.auth.user = {
                id: 3,
                name: 'Store Manager',
                email: 'manager@test.ch',
                admin_type: 'store_manager',
                permissions: ['inventory:read', 'inventory:update', 'transfers:read', 'transfers:create'],
                roles: ['store_manager'],
            };
        });

        it('isSuperAdmin returns false', () => {
            const { isSuperAdmin } = usePermissions();
            expect(isSuperAdmin()).toBe(false);
        });

        it('isPlatformAdmin returns false', () => {
            const { isPlatformAdmin } = usePermissions();
            expect(isPlatformAdmin()).toBe(false);
        });

        it('hasPermission checks direct matches only', () => {
            const { hasPermission } = usePermissions();
            expect(hasPermission('inventory:read')).toBe(true);
            expect(hasPermission('inventory:update')).toBe(true);
            expect(hasPermission('inventory:delete')).toBe(false);
            expect(hasPermission('transfers:read')).toBe(true);
            expect(hasPermission('transfers:create')).toBe(true);
            expect(hasPermission('transfers:update')).toBe(false);
        });

        it('adminType is store_manager', () => {
            const { adminType } = usePermissions();
            expect(adminType.value).toBe('store_manager');
        });
    });
});
