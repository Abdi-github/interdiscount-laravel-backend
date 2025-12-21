<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Chart from 'primevue/chart';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

interface DailyRevenue {
    date: string;
    orders: number;
    revenue: number;
}

interface TopProduct {
    product_id: number;
    product_name: string;
    total_quantity: number;
    total_revenue: number;
}

interface TopCategory {
    id: number;
    name: string;
    total_quantity: number;
    total_revenue: number;
}

interface NewUser {
    date: string;
    count: number;
}

interface Stats {
    revenue: { total: number; period: number };
    orders: { total: number; period: number; average_value: number };
    daily_revenue: DailyRevenue[];
    top_products: TopProduct[];
    orders_by_status: Record<string, number>;
    payments_by_method: Record<string, number>;
    new_users: NewUser[];
    top_categories: TopCategory[];
}

const props = defineProps<{
    stats: Stats;
    days: number;
}>();

const { t, locale } = useI18n();

const selectedDays = ref(props.days);
const periodOptions = [
    { label: '7 ' + t('analytics.days'), value: 7 },
    { label: '14 ' + t('analytics.days'), value: 14 },
    { label: '30 ' + t('analytics.days'), value: 30 },
    { label: '90 ' + t('analytics.days'), value: 90 },
];

watch(selectedDays, (val) => {
    router.get('/admin/analytics', { days: val }, { preserveState: true, preserveScroll: true });
});

function formatCurrency(val: number): string {
    return new Intl.NumberFormat(locale.value === 'de' ? 'de-CH' : 'en-CH', {
        style: 'currency',
        currency: 'CHF',
    }).format(val);
}

function formatDate(dateStr: string): string {
    const d = new Date(dateStr);
    return d.toLocaleDateString(locale.value === 'de' ? 'de-CH' : 'en-CH', {
        day: '2-digit',
        month: 'short',
    });
}

function getCategoryName(name: any): string {
    if (typeof name === 'string') {
        try {
            const parsed = JSON.parse(name);
            return parsed[locale.value] || parsed['de'] || name;
        } catch {
            return name;
        }
    }
    if (typeof name === 'object' && name !== null) {
        return name[locale.value] || name['de'] || Object.values(name)[0] as string || '';
    }
    return String(name);
}

// Stats cards
const statsCards = computed(() => [
    {
        label: t('analytics.totalRevenue'),
        value: formatCurrency(props.stats.revenue.total),
        icon: 'pi pi-dollar',
        color: 'text-green-600',
        bg: 'bg-green-50',
    },
    {
        label: t('analytics.periodRevenue'),
        value: formatCurrency(props.stats.revenue.period),
        icon: 'pi pi-chart-line',
        color: 'text-blue-600',
        bg: 'bg-blue-50',
    },
    {
        label: t('analytics.totalOrders'),
        value: props.stats.orders.total.toLocaleString(),
        icon: 'pi pi-shopping-cart',
        color: 'text-purple-600',
        bg: 'bg-purple-50',
    },
    {
        label: t('analytics.periodOrders'),
        value: props.stats.orders.period.toLocaleString(),
        icon: 'pi pi-box',
        color: 'text-orange-600',
        bg: 'bg-orange-50',
    },
    {
        label: t('analytics.averageOrder'),
        value: formatCurrency(props.stats.orders.average_value),
        icon: 'pi pi-calculator',
        color: 'text-cyan-600',
        bg: 'bg-cyan-50',
    },
]);

// Daily Revenue chart
const dailyRevenueData = computed(() => ({
    labels: props.stats.daily_revenue.map((d) => formatDate(d.date)),
    datasets: [
        {
            label: t('analytics.revenue') + ' (CHF)',
            data: props.stats.daily_revenue.map((d) => d.revenue),
            backgroundColor: 'rgba(245, 166, 35, 0.2)',
            borderColor: '#f5a623',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y',
        },
        {
            label: t('analytics.ordersLabel'),
            data: props.stats.daily_revenue.map((d) => d.orders),
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            yAxisID: 'y1',
        },
    ],
}));

const dailyRevenueOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index' as const, intersect: false },
    plugins: {
        legend: {
            position: 'top' as const,
            labels: { usePointStyle: true, font: { size: 12 } },
        },
    },
    scales: {
        y: {
            type: 'linear' as const,
            display: true,
            position: 'left' as const,
            title: { display: true, text: 'CHF' },
        },
        y1: {
            type: 'linear' as const,
            display: true,
            position: 'right' as const,
            grid: { drawOnChartArea: false },
            title: { display: true, text: t('analytics.ordersLabel') },
        },
    },
};

// Orders by Status doughnut
const statusColors: Record<string, string> = {
    PENDING: '#f59e0b',
    PLACED: '#f59e0b',
    CONFIRMED: '#3b82f6',
    PROCESSING: '#8b5cf6',
    SHIPPED: '#6366f1',
    DELIVERED: '#10b981',
    READY_FOR_PICKUP: '#06b6d4',
    PICKED_UP: '#14b8a6',
    CANCELLED: '#ef4444',
    RETURNED: '#f97316',
    PICKUP_EXPIRED: '#6b7280',
};

const orderStatusData = computed(() => {
    const labels: string[] = [];
    const data: number[] = [];
    const backgroundColor: string[] = [];
    for (const [status, count] of Object.entries(props.stats.orders_by_status)) {
        labels.push(status);
        data.push(count);
        backgroundColor.push(statusColors[status] || '#9ca3af');
    }
    return { labels, datasets: [{ data, backgroundColor, borderWidth: 0, hoverOffset: 4 }] };
});

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right' as const,
            labels: { usePointStyle: true, padding: 12, font: { size: 11 } },
        },
    },
};

// Payments by Method doughnut
const paymentColors: Record<string, string> = {
    card: '#3b82f6',
    twint: '#ec4899',
    postfinance: '#f59e0b',
    invoice: '#6b7280',
};

const paymentMethodData = computed(() => {
    const labels: string[] = [];
    const data: number[] = [];
    const backgroundColor: string[] = [];
    for (const [method, count] of Object.entries(props.stats.payments_by_method)) {
        labels.push(method.toUpperCase());
        data.push(count);
        backgroundColor.push(paymentColors[method] || '#9ca3af');
    }
    return { labels, datasets: [{ data, backgroundColor, borderWidth: 0, hoverOffset: 4 }] };
});

// New Users chart
const newUsersData = computed(() => ({
    labels: props.stats.new_users.map((u) => formatDate(u.date)),
    datasets: [
        {
            label: t('analytics.newUsers'),
            data: props.stats.new_users.map((u) => u.count),
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            borderColor: '#10b981',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
        },
    ],
}));

const newUsersOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
            labels: { usePointStyle: true, font: { size: 12 } },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: { stepSize: 1 },
        },
    },
};
</script>

<template>
    <AdminLayout :title="t('analytics.title')">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ t('analytics.title') }}</h1>
                <p class="text-gray-500 mt-1">{{ t('analytics.subtitle') }}</p>
            </div>
            <div class="w-48">
                <Select
                    v-model="selectedDays"
                    :options="periodOptions"
                    optionLabel="label"
                    optionValue="value"
                    class="w-full"
                    data-testid="period-select"
                />
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div
                v-for="(card, i) in statsCards"
                :key="i"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4"
                data-testid="stat-card"
            >
                <div class="flex items-center gap-3">
                    <div :class="[card.bg, 'w-10 h-10 rounded-lg flex items-center justify-center']">
                        <i :class="[card.icon, card.color, 'text-lg']"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">{{ card.label }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ card.value }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6" data-testid="daily-revenue-chart">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.dailyRevenue') }}</h3>
            <div class="h-72">
                <Chart type="line" :data="dailyRevenueData" :options="dailyRevenueOptions" />
            </div>
        </div>

        <!-- Charts Row: Orders by Status + Payments by Method -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="orders-status-chart">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.ordersByStatus') }}</h3>
                <div class="h-64">
                    <Chart type="doughnut" :data="orderStatusData" :options="doughnutOptions" />
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="payment-method-chart">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.paymentMethods') }}</h3>
                <div class="h-64">
                    <Chart type="doughnut" :data="paymentMethodData" :options="doughnutOptions" />
                </div>
            </div>
        </div>

        <!-- New Users Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6" data-testid="new-users-chart">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.newUsers') }}</h3>
            <div class="h-64">
                <Chart type="line" :data="newUsersData" :options="newUsersOptions" />
            </div>
        </div>

        <!-- Tables Row: Top Products + Top Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="top-products-table">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.topProducts') }}</h3>
                <DataTable :value="stats.top_products" :rows="10" stripedRows size="small">
                    <Column field="product_name" :header="t('products.name')" />
                    <Column field="total_quantity" :header="t('analytics.quantity')" style="width: 100px">
                        <template #body="{ data }">
                            <Tag :value="String(data.total_quantity)" severity="info" />
                        </template>
                    </Column>
                    <Column :header="t('analytics.revenue')" style="width: 140px">
                        <template #body="{ data }">
                            {{ formatCurrency(data.total_revenue) }}
                        </template>
                    </Column>
                </DataTable>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="top-categories-table">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('analytics.topCategories') }}</h3>
                <DataTable :value="stats.top_categories" :rows="10" stripedRows size="small">
                    <Column :header="t('categories.name')">
                        <template #body="{ data }">
                            {{ getCategoryName(data.name) }}
                        </template>
                    </Column>
                    <Column field="total_quantity" :header="t('analytics.quantity')" style="width: 100px">
                        <template #body="{ data }">
                            <Tag :value="String(data.total_quantity)" severity="info" />
                        </template>
                    </Column>
                    <Column :header="t('analytics.revenue')" style="width: 140px">
                        <template #body="{ data }">
                            {{ formatCurrency(data.total_revenue) }}
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>
