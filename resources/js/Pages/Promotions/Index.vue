<script setup lang="ts">
import { ref, watch } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';

interface Store {
    id: number;
    name: string;
}

interface PromotionRecord {
    _id: string;
    store_id: number;
    store?: { _id: string; name: string };
    product_id: number | null;
    product?: { _id: string; name: string } | null;
    category_id: number | null;
    category?: { _id: string; name: string | Record<string, string> } | null;
    title: string;
    description: string | null;
    discount_type: string;
    discount_value: number;
    buy_quantity: number | null;
    get_quantity: number | null;
    valid_from: string;
    valid_until: string;
    is_active: boolean;
    created_by: number;
    creator?: { first_name: string; last_name: string };
}

interface PromotionCollection {
    data: PromotionRecord[];
    meta: { total: number; per_page: number; current_page: number; last_page: number };
}

const props = defineProps<{
    promotions: PromotionCollection;
    stores: Store[];
    filters: {
        search?: string;
        store_id?: string;
        is_active?: string;
    };
}>();

const { t, locale } = useI18n();

const search = ref(props.filters.search || '');
const storeFilter = ref(props.filters.store_id ? Number(props.filters.store_id) : null);
const activeFilter = ref(props.filters.is_active ?? null);

const activeOptions = [
    { label: t('common.all'), value: null },
    { label: t('promotions.active'), value: '1' },
    { label: t('promotions.inactive'), value: '0' },
];

const storeOptions = [
    { label: t('promotions.allStores'), value: null },
    ...props.stores.map((s) => ({ label: s.name, value: s.id })),
];

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (storeFilter.value) params.store_id = storeFilter.value;
    if (activeFilter.value !== null) params.is_active = activeFilter.value;
    router.get('/admin/promotions', params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([storeFilter, activeFilter], applyFilters);

function formatDate(date: string | null): string {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('de-CH');
}

function discountDisplay(promo: PromotionRecord): string {
    if (promo.discount_type === 'percentage') {
        return `${promo.discount_value}%`;
    }
    return `CHF ${promo.discount_value.toFixed(2)}`;
}

function getCategoryName(name: string | Record<string, string> | undefined | null): string {
    if (!name) return '—';
    if (typeof name === 'string') {
        try {
            const parsed = JSON.parse(name);
            return parsed[locale.value] || parsed['de'] || name;
        } catch {
            return name;
        }
    }
    return name[locale.value] || name['de'] || '—';
}

function isCurrentlyActive(promo: PromotionRecord): boolean {
    if (!promo.is_active) return false;
    const now = new Date();
    return new Date(promo.valid_from) <= now && new Date(promo.valid_until) >= now;
}
</script>

<template>
    <AdminLayout :title="t('promotions.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ t('promotions.title') }}</h1>
            <p class="text-gray-500 mt-1">{{ t('promotions.subtitle') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <InputText
                        v-model="search"
                        :placeholder="t('promotions.searchPlaceholder')"
                        class="w-full"
                        data-testid="search-input"
                    />
                </div>
                <Select
                    v-model="storeFilter"
                    :options="storeOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('promotions.allStores')"
                    class="w-48"
                    data-testid="store-filter"
                />
                <Select
                    v-model="activeFilter"
                    :options="activeOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('common.all')"
                    class="w-48"
                    data-testid="active-filter"
                />
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <DataTable
                :value="promotions.data"
                :rows="20"
                stripedRows
                :paginator="promotions.meta.last_page > 1"
                :totalRecords="promotions.meta.total"
                data-testid="promotions-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-percentage text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">{{ t('promotions.noPromotions') }}</p>
                    </div>
                </template>

                <Column :header="t('promotions.titleLabel')">
                    <template #body="{ data }">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ data.title }}</p>
                            <p class="text-xs text-gray-500" v-if="data.description">{{ data.description }}</p>
                        </div>
                    </template>
                </Column>

                <Column :header="t('promotions.store')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.store?.name || '—' }}</span>
                    </template>
                </Column>

                <Column :header="t('promotions.target')">
                    <template #body="{ data }">
                        <span class="text-sm" v-if="data.product">{{ data.product.name }}</span>
                        <span class="text-sm" v-else-if="data.category">{{ getCategoryName(data.category.name) }}</span>
                        <span class="text-sm text-gray-400" v-else>—</span>
                    </template>
                </Column>

                <Column :header="t('promotions.discount')" style="width: 120px">
                    <template #body="{ data }">
                        <Tag
                            :value="discountDisplay(data)"
                            :severity="data.discount_type === 'percentage' ? 'info' : 'warn'"
                        />
                    </template>
                </Column>

                <Column :header="t('promotions.validity')">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-500">
                            {{ formatDate(data.valid_from) }} – {{ formatDate(data.valid_until) }}
                        </span>
                    </template>
                </Column>

                <Column :header="t('promotions.statusLabel')" style="width: 100px">
                    <template #body="{ data }">
                        <Tag
                            :value="isCurrentlyActive(data) ? t('promotions.active') : t('promotions.inactive')"
                            :severity="isCurrentlyActive(data) ? 'success' : 'secondary'"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
