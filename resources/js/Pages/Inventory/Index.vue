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
    slug: string;
}

interface Product {
    _id: string;
    name: string;
    code: string;
    brand?: { _id: string; name: string };
}

interface InventoryItem {
    _id: string;
    store_id: number;
    store?: { _id: string; name: string };
    product_id: number;
    product?: Product;
    quantity: number;
    reserved: number;
    available: number;
    min_stock: number;
    max_stock: number;
    location_in_store: string | null;
    is_display_unit: boolean;
    is_active: boolean;
    is_low_stock: boolean;
    is_out_of_stock: boolean;
    last_restock_at: string | null;
}

interface InventoryCollection {
    data: InventoryItem[];
    meta: { total: number; per_page: number; current_page: number; last_page: number };
}

const props = defineProps<{
    inventory: InventoryCollection;
    stores: Store[];
    filters: {
        search?: string;
        store_id?: string;
        stock_status?: string;
    };
}>();

const { t } = useI18n();

const search = ref(props.filters.search || '');
const storeFilter = ref(props.filters.store_id ? Number(props.filters.store_id) : null);
const stockFilter = ref(props.filters.stock_status || null);

const stockOptions = [
    { label: t('common.all'), value: null },
    { label: t('inventory.lowStock'), value: 'low' },
    { label: t('inventory.outOfStock'), value: 'out' },
];

const storeOptions = [
    { label: t('inventory.allStores'), value: null },
    ...props.stores.map((s) => ({ label: s.name, value: s.id })),
];

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (storeFilter.value) params.store_id = storeFilter.value;
    if (stockFilter.value) params.stock_status = stockFilter.value;
    router.get('/admin/inventory', params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([storeFilter, stockFilter], applyFilters);

function stockSeverity(item: InventoryItem): string {
    if (item.is_out_of_stock) return 'danger';
    if (item.is_low_stock) return 'warn';
    return 'success';
}

function stockLabel(item: InventoryItem): string {
    if (item.is_out_of_stock) return t('inventory.outOfStock');
    if (item.is_low_stock) return t('inventory.lowStock');
    return t('inventory.inStock');
}
</script>

<template>
    <AdminLayout :title="t('inventory.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ t('inventory.title') }}</h1>
            <p class="text-gray-500 mt-1">{{ t('inventory.subtitle') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <InputText
                        v-model="search"
                        :placeholder="t('inventory.searchPlaceholder')"
                        class="w-full"
                        data-testid="search-input"
                    />
                </div>
                <Select
                    v-model="storeFilter"
                    :options="storeOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('inventory.allStores')"
                    class="w-48"
                    data-testid="store-filter"
                />
                <Select
                    v-model="stockFilter"
                    :options="stockOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('common.all')"
                    class="w-48"
                    data-testid="stock-filter"
                />
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <DataTable
                :value="inventory.data"
                :rows="20"
                stripedRows
                :paginator="inventory.meta.last_page > 1"
                :totalRecords="inventory.meta.total"
                data-testid="inventory-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-warehouse text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">{{ t('inventory.noInventory') }}</p>
                    </div>
                </template>

                <Column :header="t('inventory.product')">
                    <template #body="{ data }">
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ data.product?.name || '—' }}</p>
                            <p class="text-xs text-gray-500">{{ data.product?.code }}</p>
                        </div>
                    </template>
                </Column>

                <Column :header="t('inventory.store')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.store?.name || '—' }}</span>
                    </template>
                </Column>

                <Column :header="t('inventory.quantity')" style="width: 100px">
                    <template #body="{ data }">
                        <span class="font-semibold">{{ data.quantity }}</span>
                    </template>
                </Column>

                <Column :header="t('inventory.reserved')" style="width: 100px">
                    <template #body="{ data }">
                        {{ data.reserved }}
                    </template>
                </Column>

                <Column :header="t('inventory.available')" style="width: 100px">
                    <template #body="{ data }">
                        <Tag :value="String(data.available)" :severity="stockSeverity(data) as any" />
                    </template>
                </Column>

                <Column :header="t('inventory.minMax')" style="width: 100px">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-500">{{ data.min_stock }} / {{ data.max_stock }}</span>
                    </template>
                </Column>

                <Column :header="t('inventory.stockStatus')" style="width: 130px">
                    <template #body="{ data }">
                        <Tag :value="stockLabel(data)" :severity="stockSeverity(data) as any" />
                    </template>
                </Column>

                <Column :header="t('inventory.location')" style="width: 120px">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-500">{{ data.location_in_store || '—' }}</span>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
