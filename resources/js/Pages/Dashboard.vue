<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StatsCards from '@/Components/Dashboard/StatsCards.vue';
import OrderChart from '@/Components/Dashboard/OrderChart.vue';
import SalesChart from '@/Components/Dashboard/SalesChart.vue';
import RecentOrders from '@/Components/Dashboard/RecentOrders.vue';
import QuickActions from '@/Components/Dashboard/QuickActions.vue';
import { useI18n } from 'vue-i18n';

interface Props {
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
    recentOrders: Array<{
        id: number;
        order_number: string;
        status: string;
        total: number;
        currency: string;
        created_at: string;
        customer_name: string;
    }>;
    ordersByStatus: Record<string, number>;
    monthlySales: Array<{
        month: string;
        revenue: number;
        count: number;
    }>;
}

const props = defineProps<Props>();
const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('nav.dashboard')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900" data-testid="dashboard-title">{{ t('nav.dashboard') }}</h1>
            <p class="text-gray-500 mt-1">{{ t('dashboard.welcome') }}</p>
        </div>

        <!-- Stats Cards -->
        <StatsCards :stats="stats" />

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <SalesChart :monthly-sales="monthlySales" />
            <OrderChart :orders-by-status="ordersByStatus" />
        </div>

        <!-- Recent Orders + Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <RecentOrders :orders="recentOrders" />
            </div>
            <div>
                <QuickActions />
            </div>
        </div>
    </AdminLayout>
</template>
