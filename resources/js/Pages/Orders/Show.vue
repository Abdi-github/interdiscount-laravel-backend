<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface StatusOption {
    value: string;
    label: string;
}

interface OrderUser {
    _id: string;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
}

interface Address {
    _id: string;
    first_name: string;
    last_name: string;
    street: string;
    street_number: string;
    postal_code: string;
    city: string;
    canton_code: string;
    country: string;
}

interface OrderItemData {
    _id: string;
    product_name: string;
    product_code: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    currency: string;
}

interface PaymentData {
    _id: string;
    amount: number;
    currency: string;
    payment_method: string;
    status: string;
    paid_at: string | null;
    created_at: string;
}

interface OrderData {
    _id: string;
    order_number: string;
    user: OrderUser | null;
    status: string;
    payment_method: string;
    payment_status: string;
    subtotal: number;
    shipping_fee: number;
    discount: number;
    total: number;
    currency: string;
    coupon_code: string | null;
    notes: string | null;
    is_store_pickup: boolean;
    estimated_delivery: string | null;
    delivered_at: string | null;
    cancelled_at: string | null;
    cancellation_reason: string | null;
    shipping_address: Address | null;
    billing_address: Address | null;
    items: OrderItemData[];
    payments: PaymentData[];
    created_at: string;
    updated_at: string;
}

interface Props {
    order: { data: OrderData };
    validTransitions: StatusOption[];
}

const props = defineProps<Props>();
const { t } = useI18n();

const order = props.order.data;

const translatedTransitions = props.validTransitions.map(tr => ({
    value: tr.value,
    label: getStatusLabel(tr.value),
}));

const statusForm = useForm({
    status: '',
});

function updateStatus() {
    if (!statusForm.status) return;
    statusForm.put(route('admin.orders.status', { id: order._id }));
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

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('de-CH', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function formatAddress(addr: Address | null): string {
    if (!addr) return '—';
    return `${addr.first_name} ${addr.last_name}\n${addr.street} ${addr.street_number}\n${addr.postal_code} ${addr.city}\n${addr.canton_code}, ${addr.country}`;
}
</script>

<template>
    <AdminLayout :title="order.order_number">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <Button
                    :label="t('orders.backToOrders')"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    text
                    @click="router.get(route('admin.orders.index'))"
                    class="mb-2"
                />
                <h1 class="text-2xl font-bold text-gray-900" data-testid="order-title">
                    {{ order.order_number }}
                </h1>
                <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <Tag :value="getStatusLabel(order.status)" :severity="statusMap[order.status] || 'info'" class="text-base px-3 py-1" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Items -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="order-items-section">
                    <h3 class="text-lg font-semibold mb-4">{{ t('orders.orderItems') }}</h3>
                    <DataTable :value="order.items" stripedRows>
                        <Column :header="t('orders.productName')">
                            <template #body="{ data }">
                                <div>
                                    <p class="font-medium text-sm">{{ data.product_name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ data.product_code }}</p>
                                </div>
                            </template>
                        </Column>
                        <Column field="quantity" :header="t('orders.quantity')" style="width: 100px" />
                        <Column :header="t('orders.unitPrice')" style="width: 140px">
                            <template #body="{ data }">
                                {{ formatCurrency(data.unit_price) }}
                            </template>
                        </Column>
                        <Column :header="t('orders.totalPrice')" style="width: 140px">
                            <template #body="{ data }">
                                <span class="font-semibold">{{ formatCurrency(data.total_price) }}</span>
                            </template>
                        </Column>
                    </DataTable>

                    <!-- Totals -->
                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ t('orders.subtotal') }}</span>
                            <span>{{ formatCurrency(order.subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ t('orders.shippingFee') }}</span>
                            <span>{{ formatCurrency(order.shipping_fee) }}</span>
                        </div>
                        <div v-if="order.discount > 0" class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ t('orders.discount') }}</span>
                            <span class="text-red-500">-{{ formatCurrency(order.discount) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                            <span>{{ t('orders.total') }}</span>
                            <span>{{ formatCurrency(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Addresses -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="shipping-address">
                        <h3 class="text-lg font-semibold mb-3">{{ t('orders.shippingAddress') }}</h3>
                        <pre class="text-sm text-gray-700 whitespace-pre-line font-sans">{{ formatAddress(order.shipping_address) }}</pre>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="billing-address">
                        <h3 class="text-lg font-semibold mb-3">{{ t('orders.billingAddress') }}</h3>
                        <pre class="text-sm text-gray-700 whitespace-pre-line font-sans">{{ formatAddress(order.billing_address) }}</pre>
                    </div>
                </div>

                <!-- Payments -->
                <div v-if="order.payments?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="payments-section">
                    <h3 class="text-lg font-semibold mb-4">{{ t('orders.payments') }}</h3>
                    <DataTable :value="order.payments" stripedRows>
                        <Column :header="t('orders.paymentMethod')">
                            <template #body="{ data }">
                                {{ getPaymentMethodLabel(data.payment_method) }}
                            </template>
                        </Column>
                        <Column :header="t('orders.total')">
                            <template #body="{ data }">
                                {{ formatCurrency(data.amount) }}
                            </template>
                        </Column>
                        <Column :header="t('orders.status')">
                            <template #body="{ data }">
                                <Tag :value="getPaymentStatusLabel(data.status)" :severity="paymentStatusMap[data.status] || 'info'" />
                            </template>
                        </Column>
                        <Column :header="t('orders.date')">
                            <template #body="{ data }">
                                {{ formatDate(data.paid_at || data.created_at) }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="order-details">
                    <h3 class="text-lg font-semibold mb-4">{{ t('orders.orderDetails') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.status') }}</span>
                            <Tag :value="getStatusLabel(order.status)" :severity="statusMap[order.status] || 'info'" />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.paymentStatus') }}</span>
                            <Tag :value="getPaymentStatusLabel(order.payment_status)" :severity="paymentStatusMap[order.payment_status] || 'info'" />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.paymentMethod') }}</span>
                            <span class="text-sm">{{ getPaymentMethodLabel(order.payment_method) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.storePickup') }}</span>
                            <span class="text-sm">{{ order.is_store_pickup ? '✓' : '✗' }}</span>
                        </div>
                        <div v-if="order.coupon_code" class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.couponCode') }}</span>
                            <span class="text-sm font-mono">{{ order.coupon_code }}</span>
                        </div>
                        <div v-if="order.estimated_delivery" class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.estimatedDelivery') }}</span>
                            <span class="text-sm">{{ formatDate(order.estimated_delivery) }}</span>
                        </div>
                        <div v-if="order.delivered_at" class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.deliveredAt') }}</span>
                            <span class="text-sm">{{ formatDate(order.delivered_at) }}</span>
                        </div>
                        <div v-if="order.cancelled_at" class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('orders.cancelledAt') }}</span>
                            <span class="text-sm">{{ formatDate(order.cancelled_at) }}</span>
                        </div>
                        <div v-if="order.cancellation_reason" class="pt-2 border-t border-gray-100">
                            <span class="text-sm text-gray-500 block mb-1">{{ t('orders.cancellationReason') }}</span>
                            <span class="text-sm">{{ order.cancellation_reason }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div v-if="order.user" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="customer-info">
                    <h3 class="text-lg font-semibold mb-4">{{ t('orders.customer') }}</h3>
                    <div class="space-y-2">
                        <p class="font-medium">{{ order.user.first_name }} {{ order.user.last_name }}</p>
                        <p class="text-sm text-gray-500">{{ order.user.email }}</p>
                        <p v-if="order.user.phone" class="text-sm text-gray-500">{{ order.user.phone }}</p>
                    </div>
                </div>

                <!-- Notes -->
                <div v-if="order.notes" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="order-notes">
                    <h3 class="text-lg font-semibold mb-3">{{ t('orders.notes') }}</h3>
                    <p class="text-sm text-gray-700">{{ order.notes }}</p>
                </div>

                <!-- Update Status -->
                <div v-if="validTransitions.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="update-status-section">
                    <h3 class="text-lg font-semibold mb-4">{{ t('orders.updateStatus') }}</h3>
                    <div class="space-y-3">
                        <Select
                            v-model="statusForm.status"
                            :options="translatedTransitions"
                            option-label="label"
                            option-value="value"
                            :placeholder="t('orders.status')"
                            class="w-full"
                            data-testid="status-transition-select"
                        />
                        <Button
                            :label="t('orders.updateStatus')"
                            icon="pi pi-check"
                            class="w-full"
                            :disabled="!statusForm.status || statusForm.processing"
                            @click="updateStatus"
                            data-testid="update-status-btn"
                        />
                    </div>
                </div>

                <!-- Meta -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold mb-4">Meta</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Created</span>
                            <span class="text-sm">{{ formatDate(order.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Updated</span>
                            <span class="text-sm">{{ formatDate(order.updated_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
