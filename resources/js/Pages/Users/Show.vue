<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface AddressData {
    _id: string;
    label: string | null;
    first_name: string;
    last_name: string;
    street: string;
    street_number: string;
    postal_code: string;
    city: string;
    canton_code: string;
    country: string;
    phone: string | null;
    is_default: boolean;
    is_billing: boolean;
}

interface OrderData {
    _id: string;
    order_number: string;
    status: string;
    payment_status: string;
    total: number;
    currency: string;
    created_at: string;
}

interface ReviewData {
    _id: string;
    rating: number;
    title: string | null;
    comment: string | null;
    is_approved: boolean;
    created_at: string;
}

interface UserData {
    _id: string;
    email: string;
    first_name: string;
    last_name: string;
    phone: string | null;
    preferred_language: string;
    avatar_url: string | null;
    is_active: boolean;
    is_verified: boolean;
    verified_at: string | null;
    last_login_at: string | null;
    created_at: string;
    updated_at: string;
    addresses: AddressData[];
    orders: OrderData[];
    reviews: ReviewData[];
}

interface Props {
    user: { data: UserData };
}

const props = defineProps<Props>();
const { t } = useI18n();

const user = props.user.data;

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

function formatCurrency(amount: number, currency: string = 'CHF'): string {
    return new Intl.NumberFormat('de-CH', { style: 'currency', currency }).format(amount);
}

function getLanguageLabel(lang: string): string {
    const map: Record<string, string> = { de: 'Deutsch', en: 'English', fr: 'Français', it: 'Italiano' };
    return map[lang] || lang;
}

function getStatusLabel(status: string): string {
    const keyMap: Record<string, string> = {
        PLACED: 'pending', PENDING: 'pending', CONFIRMED: 'confirmed', PROCESSING: 'processing',
        SHIPPED: 'shipped', DELIVERED: 'delivered', READY_FOR_PICKUP: 'readyForPickup',
        PICKED_UP: 'pickedUp', CANCELLED: 'cancelled', RETURNED: 'returned',
        PICKUP_EXPIRED: 'pickupExpired',
    };
    const key = keyMap[status] || status.toLowerCase();
    return t(`orders.${key}`);
}

function getOrderStatusSeverity(status: string): string {
    const map: Record<string, string> = {
        PLACED: 'info', PENDING: 'info', CONFIRMED: 'info', PROCESSING: 'warn',
        SHIPPED: 'warn', DELIVERED: 'success', READY_FOR_PICKUP: 'warn',
        PICKED_UP: 'success', CANCELLED: 'danger', RETURNED: 'danger',
        PICKUP_EXPIRED: 'danger',
    };
    return map[status] || 'info';
}

function toggleActive() {
    router.put(route('admin.users.toggleActive', { id: user._id }), {}, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout :title="`${user.first_name} ${user.last_name}`">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <Button
                    :label="t('users.backToUsers')"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    text
                    @click="router.get(route('admin.users.index'))"
                    class="mb-2"
                />
                <h1 class="text-2xl font-bold text-gray-900" data-testid="user-title">
                    {{ user.first_name }} {{ user.last_name }}
                </h1>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Tag
                    :value="user.is_verified ? t('users.verified') : t('users.unverified')"
                    :severity="user.is_verified ? 'success' : 'warn'"
                    class="text-base px-3 py-1"
                />
                <Tag
                    :value="user.is_active ? t('users.active') : t('users.inactive')"
                    :severity="user.is_active ? 'success' : 'danger'"
                    class="text-base px-3 py-1"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Addresses -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-addresses">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.addresses') }}</h3>
                    <template v-if="user.addresses?.length">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="address in user.addresses"
                                :key="address._id"
                                class="border border-gray-200 rounded-lg p-4"
                            >
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-medium text-sm">{{ address.label || `${address.first_name} ${address.last_name}` }}</span>
                                    <Tag v-if="address.is_default" :value="t('users.defaultAddress')" severity="info" class="text-xs" />
                                    <Tag v-if="address.is_billing" :value="t('users.billingAddress')" severity="warn" class="text-xs" />
                                </div>
                                <p class="text-sm text-gray-600">{{ address.street }} {{ address.street_number }}</p>
                                <p class="text-sm text-gray-600">{{ address.postal_code }} {{ address.city }}</p>
                                <p class="text-sm text-gray-600">{{ address.canton_code }}, {{ address.country }}</p>
                                <p v-if="address.phone" class="text-sm text-gray-500 mt-1">{{ address.phone }}</p>
                            </div>
                        </div>
                    </template>
                    <p v-else class="text-sm text-gray-400">{{ t('users.noAddresses') }}</p>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-orders">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.recentOrders') }}</h3>
                    <template v-if="user.orders?.length">
                        <DataTable :value="user.orders" stripedRows>
                            <Column :header="t('orders.orderNumber')">
                                <template #body="{ data }">
                                    <span
                                        class="font-mono text-sm text-blue-600 cursor-pointer hover:underline"
                                        @click="router.get(route('admin.orders.show', { id: data._id }))"
                                    >{{ data.order_number }}</span>
                                </template>
                            </Column>
                            <Column :header="t('orders.total')">
                                <template #body="{ data }">
                                    <span class="text-sm font-medium">{{ formatCurrency(data.total, data.currency) }}</span>
                                </template>
                            </Column>
                            <Column :header="t('orders.status')">
                                <template #body="{ data }">
                                    <Tag :value="getStatusLabel(data.status)" :severity="getOrderStatusSeverity(data.status)" />
                                </template>
                            </Column>
                            <Column :header="t('common.date')">
                                <template #body="{ data }">
                                    <span class="text-sm">{{ formatDate(data.created_at) }}</span>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                    <p v-else class="text-sm text-gray-400">{{ t('users.noOrders') }}</p>
                </div>

                <!-- Recent Reviews -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-reviews">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.recentReviews') }}</h3>
                    <template v-if="user.reviews?.length">
                        <DataTable :value="user.reviews" stripedRows>
                            <Column :header="t('reviews.rating')">
                                <template #body="{ data }">
                                    <div class="flex items-center gap-1">
                                        <i v-for="i in 5" :key="i" class="pi text-sm" :class="i <= data.rating ? 'pi-star-fill text-yellow-400' : 'pi-star text-gray-300'" />
                                    </div>
                                </template>
                            </Column>
                            <Column :header="t('reviews.title_field')">
                                <template #body="{ data }">
                                    <span class="text-sm">{{ data.title || '—' }}</span>
                                </template>
                            </Column>
                            <Column :header="t('reviews.status')">
                                <template #body="{ data }">
                                    <Tag
                                        :value="data.is_approved ? t('reviews.approved') : t('reviews.pending')"
                                        :severity="data.is_approved ? 'success' : 'warn'"
                                    />
                                </template>
                            </Column>
                            <Column :header="t('common.date')">
                                <template #body="{ data }">
                                    <span class="text-sm">{{ formatDate(data.created_at) }}</span>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                    <p v-else class="text-sm text-gray-400">{{ t('users.noReviews') }}</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- User Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-details">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.userDetails') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.role') }}</span>
                            <Tag value="Customer" severity="info" />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.status') }}</span>
                            <Tag
                                :value="user.is_active ? t('users.active') : t('users.inactive')"
                                :severity="user.is_active ? 'success' : 'danger'"
                            />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.verified') }}</span>
                            <Tag
                                :value="user.is_verified ? t('users.verified') : t('users.unverified')"
                                :severity="user.is_verified ? 'success' : 'warn'"
                            />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.language') }}</span>
                            <span class="text-sm">{{ getLanguageLabel(user.preferred_language) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Personal Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-personal-info">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.personalInfo') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.firstName') }}</span>
                            <span class="text-sm">{{ user.first_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.lastName') }}</span>
                            <span class="text-sm">{{ user.last_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.email') }}</span>
                            <span class="text-sm">{{ user.email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.phone') }}</span>
                            <span class="text-sm">{{ user.phone || '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-account-info">
                    <h3 class="text-lg font-semibold mb-4">{{ t('users.accountInfo') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.verifiedAt') }}</span>
                            <span class="text-sm">{{ formatDate(user.verified_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.lastLogin') }}</span>
                            <span class="text-sm">{{ formatDate(user.last_login_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('users.registeredAt') }}</span>
                            <span class="text-sm">{{ formatDate(user.created_at) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="user-actions">
                    <h3 class="text-lg font-semibold mb-4">{{ t('common.actions') }}</h3>
                    <Button
                        :label="user.is_active ? t('users.deactivate') : t('users.activate')"
                        :icon="user.is_active ? 'pi pi-ban' : 'pi pi-check'"
                        :severity="user.is_active ? 'danger' : 'success'"
                        class="w-full"
                        @click="toggleActive"
                        data-testid="toggle-active-btn"
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
