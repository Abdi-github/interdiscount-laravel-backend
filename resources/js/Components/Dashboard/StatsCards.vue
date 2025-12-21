<script setup lang="ts">
import { useI18n } from 'vue-i18n';

interface StatCard {
    label: string;
    value: string | number;
    icon: string;
    iconBg: string;
    iconColor: string;
    change?: string;
    changeType?: 'positive' | 'negative' | 'neutral';
}

const props = defineProps<{
    stats: {
        totalOrders: number;
        totalProducts: number;
        totalUsers: number;
        totalStores: number;
        totalRevenue: number;
        pendingOrders: number;
        pendingReviews: number;
        lowStockItems: number;
    };
}>();

const { t } = useI18n();

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('de-CH', {
        style: 'currency',
        currency: 'CHF',
    }).format(value);
}

const cards: StatCard[] = [
    {
        label: t('dashboard.totalOrders'),
        value: props.stats.totalOrders,
        icon: 'pi-shopping-cart',
        iconBg: 'bg-blue-50',
        iconColor: 'text-blue-500',
    },
    {
        label: t('dashboard.totalRevenue'),
        value: formatCurrency(props.stats.totalRevenue),
        icon: 'pi-wallet',
        iconBg: 'bg-green-50',
        iconColor: 'text-green-500',
    },
    {
        label: t('dashboard.totalProducts'),
        value: props.stats.totalProducts,
        icon: 'pi-box',
        iconBg: 'bg-purple-50',
        iconColor: 'text-purple-500',
    },
    {
        label: t('dashboard.totalUsers'),
        value: props.stats.totalUsers,
        icon: 'pi-users',
        iconBg: 'bg-amber-50',
        iconColor: 'text-amber-500',
    },
];

const secondaryCards: StatCard[] = [
    {
        label: t('dashboard.totalStores'),
        value: props.stats.totalStores,
        icon: 'pi-building',
        iconBg: 'bg-indigo-50',
        iconColor: 'text-indigo-500',
    },
    {
        label: t('dashboard.pendingOrders'),
        value: props.stats.pendingOrders,
        icon: 'pi-clock',
        iconBg: 'bg-orange-50',
        iconColor: 'text-orange-500',
    },
    {
        label: t('dashboard.pendingReviews'),
        value: props.stats.pendingReviews,
        icon: 'pi-star',
        iconBg: 'bg-yellow-50',
        iconColor: 'text-yellow-500',
    },
    {
        label: t('dashboard.lowStockItems'),
        value: props.stats.lowStockItems,
        icon: 'pi-exclamation-triangle',
        iconBg: 'bg-red-50',
        iconColor: 'text-red-500',
    },
];
</script>

<template>
    <!-- Primary stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" data-testid="primary-stats">
        <div
            v-for="card in cards"
            :key="card.label"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
            :data-testid="`stat-card-${card.icon.replace('pi-', '')}`"
        >
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="card.iconBg">
                    <i :class="['pi', card.icon, 'text-xl', card.iconColor]"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">{{ card.label }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ card.value }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" data-testid="secondary-stats">
        <div
            v-for="card in secondaryCards"
            :key="card.label"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-5"
        >
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="card.iconBg">
                    <i :class="['pi', card.icon, 'text-lg', card.iconColor]"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500">{{ card.label }}</p>
                    <p class="text-lg font-semibold text-gray-900">{{ card.value }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
