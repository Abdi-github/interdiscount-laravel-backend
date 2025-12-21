<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

interface StatusOption {
    value: string;
    label: string;
}

interface OrderUser {
    _id: string;
    first_name: string;
    last_name: string;
    email: string;
}

interface OrderData {
    _id: string;
    order_number: string;
    user_id: number;
    user: OrderUser | null;
    status: string;
    payment_method: string;
    payment_status: string;
    subtotal: number;
    shipping_fee: number;
    discount: number;
    total: number;
    currency: string;
    is_store_pickup: boolean;
    items: any[];
    created_at: string;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    orders: { data: OrderData[]; meta: PaginationMeta };
    filters: Record<string, string>;
    statuses: StatusOption[];
    paymentStatuses: StatusOption[];
}

const props = defineProps<Props>();
const { t } = useI18n();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const paymentStatusFilter = ref(props.filters.payment_status || '');

let searchTimeout: ReturnType<typeof setTimeout>;

const statusOptions = computed(() => [
    { value: '', label: `${t('common.all')} ${t('common.status')}` },
    ...props.statuses.map(s => ({ value: s.value, label: getStatusLabel(s.value) })),
]);

const paymentStatusOptions = computed(() => [
    { value: '', label: `${t('common.all')} ${t('orders.paymentStatus')}` },
    ...props.paymentStatuses.map(s => ({ value: s.value, label: getPaymentStatusLabel(s.value) })),
]);

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value) params.status = statusFilter.value;
    if (paymentStatusFilter.value) params.payment_status = paymentStatusFilter.value;
    if (props.filters.per_page) params.per_page = props.filters.per_page;

    router.get(route('admin.orders.index'), params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([statusFilter, paymentStatusFilter], applyFilters);

function resetFilters() {
    search.value = '';
    statusFilter.value = '';
    paymentStatusFilter.value = '';
    router.get(route('admin.orders.index'));
}

function onPage(event: any) {
    const params: Record<string, any> = { ...props.filters, page: event.page + 1, per_page: event.rows };
    router.get(route('admin.orders.index'), params, { preserveState: true, preserveScroll: true });
}

const statusMap: Record<string, string> = {
    PENDING: 'warn',
    CONFIRMED: 'info',
    PROCESSING: 'info',
    SHIPPED: 'info',
    DELIVERED: 'success',
    READY_FOR_PICKUP: 'warn',
    PICKED_UP: 'success',
    CANCELLED: 'danger',
    RETURNED: 'secondary',
    PICKUP_EXPIRED: 'danger',
};

const paymentStatusMap: Record<string, string> = {
    PENDING: 'warn',
    PROCESSING: 'info',
    PAID: 'success',
    FAILED: 'danger',
    REFUNDED: 'secondary',
    PARTIALLY_REFUNDED: 'secondary',
};

function getStatusLabel(status: string): string {
    const keyMap: Record<string, string> = {
        PENDING: 'pending',
        CONFIRMED: 'confirmed',
        PROCESSING: 'processing',
        SHIPPED: 'shipped',
        DELIVERED: 'delivered',
        READY_FOR_PICKUP: 'readyForPickup',
        PICKED_UP: 'pickedUp',
        CANCELLED: 'cancelled',
        RETURNED: 'returned',
        PICKUP_EXPIRED: 'pickupExpired',
    };
    const key = keyMap[status];
    return key ? t(`orders.${key}`) : status;
}

function getPaymentStatusLabel(status: string): string {
    const keyMap: Record<string, string> = {
        PENDING: 'paymentPending',
        PROCESSING: 'paymentProcessing',
        PAID: 'paymentPaid',
        FAILED: 'paymentFailed',
        REFUNDED: 'paymentRefunded',
        PARTIALLY_REFUNDED: 'paymentPartiallyRefunded',
    };
    const key = keyMap[status];
    return key ? t(`orders.${key}`) : status;
}

function getPaymentMethodLabel(method: string): string {
    const key = `orders.${method}` as any;
    const label = t(key);
    return label !== key ? label : method;
}

function formatCurrency(amount: number): string {
    return `CHF ${Number(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, "'")}`;
}

function formatDate(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('de-CH', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}
</script>

<template>
    <AdminLayout :title="t('orders.title')">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900" data-testid="orders-title">{{ t('orders.title') }}</h1>
                <p class="text-sm text-gray-500" data-testid="orders-count">
                    {{ orders.meta.total }} {{ t('orders.title').toLowerCase() }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-6" data-testid="orders-filters">
            <InputText
                v-model="search"
                :placeholder="t('orders.searchOrders')"
                class="w-64"
                data-testid="orders-search"
            />
            <Select
                v-model="statusFilter"
                :options="statusOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('orders.status')"
                class="w-48"
                data-testid="orders-status-filter"
            />
            <Select
                v-model="paymentStatusFilter"
                :options="paymentStatusOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('orders.paymentStatus')"
                class="w-48"
                data-testid="orders-payment-filter"
            />
            <Button
                :label="t('common.reset')"
                icon="pi pi-filter-slash"
                severity="secondary"
                text
                @click="resetFilters"
                data-testid="reset-filters-btn"
            />
        </div>

        <!-- DataTable -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" data-testid="orders-table">
            <DataTable
                :value="orders.data"
                :lazy="true"
                :paginator="true"
                :rows="orders.meta.per_page"
                :totalRecords="orders.meta.total"
                :first="(orders.meta.current_page - 1) * orders.meta.per_page"
                @page="onPage"
                :rowsPerPageOptions="[10, 20, 50]"
                stripedRows
                data-testid="orders-datatable"
            >
                <Column field="order_number" :header="t('orders.orderNumber')" style="width: 180px">
                    <template #body="{ data }">
                        <span class="font-mono text-sm font-medium text-primary-700">{{ data.order_number }}</span>
                    </template>
                </Column>

                <Column field="user" :header="t('orders.customer')">
                    <template #body="{ data }">
                        <div v-if="data.user">
                            <p class="font-medium text-sm">{{ data.user.first_name }} {{ data.user.last_name }}</p>
                            <p class="text-xs text-gray-500">{{ data.user.email }}</p>
                        </div>
                        <span v-else class="text-gray-400">—</span>
                    </template>
                </Column>

                <Column field="total" :header="t('orders.total')" sortable>
                    <template #body="{ data }">
                        <span class="font-semibold">{{ formatCurrency(data.total) }}</span>
                    </template>
                </Column>

                <Column field="items" :header="t('orders.items')">
                    <template #body="{ data }">
                        {{ data.items?.length || 0 }}
                    </template>
                </Column>

                <Column field="payment_method" :header="t('orders.paymentMethod')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ getPaymentMethodLabel(data.payment_method) }}</span>
                    </template>
                </Column>

                <Column field="status" :header="t('orders.status')">
                    <template #body="{ data }">
                        <Tag :value="getStatusLabel(data.status)" :severity="statusMap[data.status] || 'info'" />
                    </template>
                </Column>

                <Column field="payment_status" :header="t('orders.paymentStatus')">
                    <template #body="{ data }">
                        <Tag :value="getPaymentStatusLabel(data.payment_status)" :severity="paymentStatusMap[data.payment_status] || 'info'" />
                    </template>
                </Column>

                <Column field="created_at" :header="t('orders.date')" sortable>
                    <template #body="{ data }">
                        <span class="text-sm">{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>

                <Column :header="t('common.actions')" style="width: 100px">
                    <template #body="{ data }">
                        <div class="flex items-center gap-1">
                            <Button
                                icon="pi pi-eye"
                                severity="info"
                                text
                                rounded
                                size="small"
                                @click="router.get(route('admin.orders.show', { id: data._id }))"
                                data-testid="view-btn"
                            />
                        </div>
                    </template>
                </Column>

                <template #empty>
                    <div class="text-center py-12">
                        <i class="pi pi-shopping-cart text-4xl text-gray-300 mb-3" />
                        <p class="text-gray-500 font-medium">{{ t('orders.noOrders') }}</p>
                        <p class="text-gray-400 text-sm">{{ t('orders.noOrdersDescription') }}</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>
