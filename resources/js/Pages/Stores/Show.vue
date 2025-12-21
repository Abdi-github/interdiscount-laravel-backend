<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

interface StoreCity {
    _id: string;
    name: Record<string, string>;
}

interface StoreCanton {
    _id: string;
    name: Record<string, string>;
    code: string;
}

interface OpeningHour {
    day: Record<string, string>;
    open: string;
    close: string;
    is_closed: boolean;
}

interface StoreData {
    _id: string;
    name: string;
    slug: string;
    store_id: string;
    street: string;
    street_number: string;
    postal_code: string;
    city: StoreCity | null;
    canton: StoreCanton | null;
    phone: string | null;
    email: string | null;
    format: string | null;
    is_xxl: boolean;
    is_active: boolean;
    latitude: number | null;
    longitude: number | null;
    remarks: string | null;
    opening_hours: OpeningHour[] | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    store: { data: StoreData };
}

const props = defineProps<Props>();
const { t, locale } = useI18n();

const store = props.store.data;

function getLocalizedName(name: Record<string, string> | null | undefined): string {
    if (!name) return '—';
    return name[locale.value] || name['de'] || Object.values(name)[0] || '—';
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
</script>

<template>
    <AdminLayout :title="store.name">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <Button
                    :label="t('stores.backToStores')"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    text
                    @click="router.get(route('admin.stores.index'))"
                    class="mb-2"
                />
                <h1 class="text-2xl font-bold text-gray-900" data-testid="store-title">{{ store.name }}</h1>
                <p class="text-sm text-gray-500 font-mono">{{ store.store_id }}</p>
            </div>
            <div class="flex items-center gap-2">
                <Tag v-if="store.is_xxl" value="XXL" severity="warn" class="text-base px-3 py-1" />
                <Tag
                    :value="store.is_active ? t('stores.active') : t('stores.inactive')"
                    :severity="store.is_active ? 'success' : 'danger'"
                    class="text-base px-3 py-1"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Opening Hours -->
                <div v-if="store.opening_hours?.length" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="opening-hours">
                    <h3 class="text-lg font-semibold mb-4">{{ t('stores.openingHours') }}</h3>
                    <DataTable :value="store.opening_hours" stripedRows>
                        <Column :header="t('stores.day')">
                            <template #body="{ data }">
                                <span class="font-medium text-sm">{{ getLocalizedName(data.day) }}</span>
                            </template>
                        </Column>
                        <Column :header="t('stores.open')">
                            <template #body="{ data }">
                                <span class="text-sm">{{ data.is_closed ? '—' : data.open }}</span>
                            </template>
                        </Column>
                        <Column :header="t('stores.close')">
                            <template #body="{ data }">
                                <span class="text-sm">{{ data.is_closed ? '—' : data.close }}</span>
                            </template>
                        </Column>
                        <Column :header="t('stores.status')">
                            <template #body="{ data }">
                                <Tag
                                    :value="data.is_closed ? t('stores.closed') : t('stores.active')"
                                    :severity="data.is_closed ? 'danger' : 'success'"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Remarks -->
                <div v-if="store.remarks" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="store-remarks">
                    <h3 class="text-lg font-semibold mb-3">{{ t('stores.remarks') }}</h3>
                    <p class="text-sm text-gray-700">{{ store.remarks }}</p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Store Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="store-details">
                    <h3 class="text-lg font-semibold mb-4">{{ t('stores.storeDetails') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('stores.format') }}</span>
                            <span class="text-sm">{{ store.format || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('stores.xxl') }}</span>
                            <span class="text-sm">{{ store.is_xxl ? '✓' : '✗' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('stores.storeId') }}</span>
                            <span class="text-sm font-mono">{{ store.store_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Slug</span>
                            <span class="text-sm font-mono">{{ store.slug }}</span>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="store-address">
                    <h3 class="text-lg font-semibold mb-4">{{ t('stores.address') }}</h3>
                    <div class="space-y-2">
                        <p class="text-sm">{{ store.street }} {{ store.street_number }}</p>
                        <p class="text-sm">{{ store.postal_code }} {{ getLocalizedName(store.city?.name) }}</p>
                        <p class="text-sm text-gray-500">{{ store.canton?.code }} — {{ getLocalizedName(store.canton?.name) }}</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="contact-info">
                    <h3 class="text-lg font-semibold mb-4">{{ t('stores.contactInfo') }}</h3>
                    <div class="space-y-2">
                        <div v-if="store.phone" class="flex items-center gap-2">
                            <i class="pi pi-phone text-gray-400 text-sm" />
                            <span class="text-sm">{{ store.phone }}</span>
                        </div>
                        <div v-if="store.email" class="flex items-center gap-2">
                            <i class="pi pi-envelope text-gray-400 text-sm" />
                            <span class="text-sm">{{ store.email }}</span>
                        </div>
                    </div>
                </div>

                <!-- Coordinates -->
                <div v-if="store.latitude && store.longitude" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="store-coordinates">
                    <h3 class="text-lg font-semibold mb-4">{{ t('stores.coordinates') }}</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('stores.latitude') }}</span>
                            <span class="text-sm font-mono">{{ store.latitude }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">{{ t('stores.longitude') }}</span>
                            <span class="text-sm font-mono">{{ store.longitude }}</span>
                        </div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold mb-4">Meta</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Created</span>
                            <span class="text-sm">{{ formatDate(store.created_at) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-500">Updated</span>
                            <span class="text-sm">{{ formatDate(store.updated_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
