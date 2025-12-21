<script setup lang="ts">
import { computed } from 'vue';
import Chart from 'primevue/chart';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    monthlySales: Array<{
        month: string;
        revenue: number;
        count: number;
    }>;
}>();

const { t } = useI18n();

function formatMonth(yyyymm: string): string {
    const [year, month] = yyyymm.split('-');
    const date = new Date(parseInt(year), parseInt(month) - 1);
    return date.toLocaleDateString('de-CH', { month: 'short', year: '2-digit' });
}

const chartData = computed(() => ({
    labels: props.monthlySales.map((s) => formatMonth(s.month)),
    datasets: [
        {
            label: t('dashboard.totalRevenue') + ' (CHF)',
            data: props.monthlySales.map((s) => s.revenue),
            backgroundColor: 'rgba(245, 166, 35, 0.2)',
            borderColor: '#f5a623',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y',
        },
        {
            label: t('dashboard.totalOrders'),
            data: props.monthlySales.map((s) => s.count),
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            fill: false,
            tension: 0.4,
            yAxisID: 'y1',
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: 'index' as const,
        intersect: false,
    },
    plugins: {
        legend: {
            position: 'top' as const,
            labels: {
                usePointStyle: true,
                font: { size: 12 },
            },
        },
    },
    scales: {
        y: {
            type: 'linear' as const,
            display: true,
            position: 'left' as const,
            title: {
                display: true,
                text: 'CHF',
            },
        },
        y1: {
            type: 'linear' as const,
            display: true,
            position: 'right' as const,
            grid: { drawOnChartArea: false },
            title: {
                display: true,
                text: t('dashboard.totalOrders'),
            },
        },
    },
};
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="sales-chart">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('dashboard.salesOverview') }}</h3>
        <div class="h-72">
            <Chart type="line" :data="chartData" :options="chartOptions" />
        </div>
    </div>
</template>
