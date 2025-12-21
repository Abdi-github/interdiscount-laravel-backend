<script setup lang="ts">
import { computed } from 'vue';
import Chart from 'primevue/chart';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    ordersByStatus: Record<string, number>;
}>();

const { t } = useI18n();

const statusColors: Record<string, string> = {
    PENDING: '#f59e0b',
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

const statusLabels: Record<string, string> = {
    PENDING: t('orders.pending'),
    CONFIRMED: t('orders.confirmed'),
    PROCESSING: t('orders.processing'),
    SHIPPED: t('orders.shipped'),
    DELIVERED: t('orders.delivered'),
    CANCELLED: t('orders.cancelled'),
    RETURNED: t('orders.returned'),
};

const chartData = computed(() => {
    const labels: string[] = [];
    const data: number[] = [];
    const backgroundColor: string[] = [];

    for (const [status, count] of Object.entries(props.ordersByStatus)) {
        labels.push(statusLabels[status] || status);
        data.push(count);
        backgroundColor.push(statusColors[status] || '#9ca3af');
    }

    return {
        labels,
        datasets: [
            {
                data,
                backgroundColor,
                borderWidth: 0,
                hoverOffset: 4,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right' as const,
            labels: {
                usePointStyle: true,
                padding: 16,
                font: { size: 12 },
            },
        },
    },
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="order-chart">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('dashboard.ordersByStatus') }}</h3>
        <div class="h-64">
            <Chart type="doughnut" :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
