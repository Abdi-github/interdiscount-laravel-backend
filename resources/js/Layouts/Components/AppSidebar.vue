<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { usePermissions } from '@/Composables/usePermissions';

const props = defineProps<{
    collapsed: boolean;
    mobileOpen: boolean;
}>();

const emit = defineEmits<{
    closeMobile: [];
}>();

const { t } = useI18n();
const { hasPermission } = usePermissions();
const page = usePage();

const currentUrl = computed(() => page.url);

interface NavItem {
    label: string;
    icon: string;
    route: string;
    permission?: string;
}

const navItems = computed<NavItem[]>(() => [
    { label: t('nav.dashboard'), icon: 'pi-home', route: '/admin/dashboard' },
    { label: t('nav.products'), icon: 'pi-box', route: '/admin/products', permission: 'products:read' },
    { label: t('nav.categories'), icon: 'pi-folder', route: '/admin/categories', permission: 'categories:read' },
    { label: t('nav.brands'), icon: 'pi-tag', route: '/admin/brands', permission: 'brands:read' },
    { label: t('nav.stores'), icon: 'pi-building', route: '/admin/stores', permission: 'stores:read' },
    { label: t('nav.inventory'), icon: 'pi-warehouse', route: '/admin/inventory', permission: 'inventory:read' },
    { label: t('nav.transfers'), icon: 'pi-arrows-h', route: '/admin/transfers', permission: 'transfers:read' },
    { label: t('nav.promotions'), icon: 'pi-percentage', route: '/admin/promotions', permission: 'promotions:read' },
    { label: t('nav.orders'), icon: 'pi-shopping-cart', route: '/admin/orders', permission: 'orders:read' },
    { label: t('nav.users'), icon: 'pi-users', route: '/admin/users', permission: 'users:read' },
    { label: t('nav.reviews'), icon: 'pi-star', route: '/admin/reviews', permission: 'reviews:read' },
    { label: t('nav.coupons'), icon: 'pi-ticket', route: '/admin/coupons', permission: 'coupons:read' },
    { label: t('nav.locations'), icon: 'pi-map-marker', route: '/admin/locations', permission: 'categories:read' },
    { label: t('nav.analytics'), icon: 'pi-chart-bar', route: '/admin/analytics', permission: 'analytics:read' },
    { label: t('nav.roles'), icon: 'pi-shield', route: '/admin/roles', permission: 'roles:read' },
    { label: t('nav.permissions'), icon: 'pi-lock', route: '/admin/permissions', permission: 'permissions:read' },
    { label: t('nav.settings'), icon: 'pi-cog', route: '/admin/settings' },
]);

const filteredNavItems = computed(() =>
    navItems.value.filter((item) => !item.permission || hasPermission(item.permission))
);

function isActive(itemRoute: string): boolean {
    const url = currentUrl.value;
    if (itemRoute === '/admin/dashboard') {
        return url === '/admin/dashboard';
    }
    return url.startsWith(itemRoute);
}

function onNavClick() {
    emit('closeMobile');
}
</script>

<template>
    <!-- Desktop sidebar -->
    <aside
        class="fixed top-0 left-0 z-50 h-screen bg-white border-r border-gray-200 transition-all duration-300 hidden lg:block"
        :class="collapsed ? 'w-20' : 'w-64'"
        data-testid="sidebar"
    >
        <!-- Logo -->
        <div class="flex items-center h-16 px-4 border-b border-gray-200">
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-primary-500 flex items-center justify-center">
                    <i class="pi pi-shop text-white text-lg"></i>
                </div>
                <span
                    v-if="!collapsed"
                    class="font-bold text-gray-900 text-lg whitespace-nowrap"
                >
                    Interdiscount
                </span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3" data-testid="sidebar-nav">
            <ul class="space-y-1">
                <li v-for="item in filteredNavItems" :key="item.route">
                    <Link
                        :href="item.route"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                        :class="isActive(item.route)
                            ? 'bg-primary-50 text-primary-700'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                        :title="collapsed ? item.label : undefined"
                        :data-testid="`nav-${item.route.split('/').pop()}`"
                        @click="onNavClick"
                    >
                        <i :class="['pi', item.icon, 'text-base']"></i>
                        <span v-if="!collapsed" class="whitespace-nowrap">{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Mobile sidebar -->
    <aside
        v-if="mobileOpen"
        class="fixed top-0 left-0 z-50 h-screen w-64 bg-white border-r border-gray-200 lg:hidden"
        data-testid="mobile-sidebar"
    >
        <!-- Logo + close -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-500 flex items-center justify-center">
                    <i class="pi pi-shop text-white text-lg"></i>
                </div>
                <span class="font-bold text-gray-900 text-lg">Interdiscount</span>
            </div>
            <button
                class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100"
                @click="$emit('closeMobile')"
                data-testid="close-mobile-sidebar"
            >
                <i class="pi pi-times"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <ul class="space-y-1">
                <li v-for="item in filteredNavItems" :key="item.route">
                    <Link
                        :href="item.route"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                        :class="isActive(item.route)
                            ? 'bg-primary-50 text-primary-700'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                        @click="onNavClick"
                    >
                        <i :class="['pi', item.icon, 'text-base']"></i>
                        <span>{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>
