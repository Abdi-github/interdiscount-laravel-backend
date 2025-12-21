<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';

const props = defineProps<{
    orders: Array<{
        id: number;
        order_number: string;
        status: string;
        total: number;
        currency: string;
        created_at: string;
        customer_name: string;
    }>;
}>();

const { t } = useI18n();

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('de-CH', {
        style: 'currency',
        currency: 'CHF',
    }).format(value);
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('de-CH', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function getStatusSeverity(status: string): 'success' | 'info' | 'warn' | 'danger' | 'secondary' | 'contrast' | undefined {
    const map: Record<string, 'success' | 'info' | 'warn' | 'danger' | 'secondary'> = {
        PENDING: 'warn',
        CONFIRMED: 'info',
        PROCESSING: 'info',
        SHIPPED: 'info',
        DELIVERED: 'success',
        PICKED_UP: 'success',
        CANCELLED: 'danger',
        RETURNED: 'warn',
    };
    return map[status] || 'secondary';
}

function getStatusLabel(status: string): string {
    const key = status.toLowerCase();
    return t(`orders.${key}`, status);
}
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="recent-orders">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('dashboard.recentOrders') }}</h3>
        <DataTable :value="orders" :rows="10" stripedRows size="small" data-testid="recent-orders-table">
            <Column field="order_number" :header="t('orders.orderNumber')" />
            <Column field="customer_name" :header="t('orders.customer')" />
            <Column field="total" :header="t('orders.total')">
                <template #body="{ data }">
                    {{ formatCurrency(data.total) }}
                </template>
            </Column>
            <Column field="status" :header="t('orders.status')">
                <template #body="{ data }">
                    <Tag :severity="getStatusSeverity(data.status)" :value="getStatusLabel(data.status)" />
                </template>
            </Column>
            <Column field="created_at" :header="t('orders.date')">
                <template #body="{ data }">
                    {{ formatDate(data.created_at) }}
                </template>
            </Column>
        </DataTable>
    </div>
</template>
