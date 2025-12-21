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

interface CantonOption {
    _id: string;
    name: Record<string, string>;
    code: string;
}

interface StoreCity {
    _id: string;
    name: Record<string, string>;
}

interface StoreCanton {
    _id: string;
    name: Record<string, string>;
    code: string;
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
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    stores: { data: StoreData[]; meta: PaginationMeta };
    filters: Record<string, string>;
    cantons: { data: CantonOption[] };
}

const props = defineProps<Props>();
const { t, locale } = useI18n();

const search = ref(props.filters.search || '');
const cantonFilter = ref(props.filters.canton_id || '');
const statusFilter = ref(props.filters.is_active ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

const cantonOptions = computed(() => [
    { value: '', label: t('stores.allCantons') },
    ...props.cantons.data.map(c => ({
        value: c._id,
        label: `${c.code} — ${c.name[locale.value] || c.name['de']}`,
    })),
]);

const statusOptions = computed(() => [
    { value: '', label: t('common.all') + ' ' + t('stores.status') },
    { value: '1', label: t('stores.active') },
    { value: '0', label: t('stores.inactive') },
]);

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (cantonFilter.value) params.canton_id = cantonFilter.value;
    if (statusFilter.value !== '') params.is_active = statusFilter.value;
    if (props.filters.per_page) params.per_page = props.filters.per_page;

    router.get(route('admin.stores.index'), params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([cantonFilter, statusFilter], applyFilters);

function resetFilters() {
    search.value = '';
    cantonFilter.value = '';
    statusFilter.value = '';
    router.get(route('admin.stores.index'));
}

function onPage(event: any) {
    const params: Record<string, any> = { ...props.filters, page: event.page + 1, per_page: event.rows };
    router.get(route('admin.stores.index'), params, { preserveState: true, preserveScroll: true });
}

function getLocalizedName(name: Record<string, string> | null | undefined): string {
    if (!name) return '—';
    return name[locale.value] || name['de'] || Object.values(name)[0] || '—';
}
</script>

<template>
    <AdminLayout :title="t('stores.title')">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900" data-testid="stores-title">{{ t('stores.title') }}</h1>
                <p class="text-sm text-gray-500" data-testid="stores-count">
                    {{ stores.meta.total }} {{ t('stores.title').toLowerCase() }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-6" data-testid="stores-filters">
            <InputText
                v-model="search"
                :placeholder="t('stores.searchStores')"
                class="w-64"
                data-testid="stores-search"
            />
            <Select
                v-model="cantonFilter"
                :options="cantonOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('stores.canton')"
                class="w-56"
                filter
                data-testid="stores-canton-filter"
            />
            <Select
                v-model="statusFilter"
                :options="statusOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('stores.status')"
                class="w-48"
                data-testid="stores-status-filter"
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" data-testid="stores-table">
            <DataTable
                :value="stores.data"
                :lazy="true"
                :paginator="true"
                :rows="stores.meta.per_page"
                :totalRecords="stores.meta.total"
                :first="(stores.meta.current_page - 1) * stores.meta.per_page"
                @page="onPage"
                :rowsPerPageOptions="[10, 20, 50]"
                stripedRows
                data-testid="stores-datatable"
            >
                <Column field="name" :header="t('stores.name')">
                    <template #body="{ data }">
                        <div>
                            <p class="font-medium text-sm">{{ data.name }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ data.store_id }}</p>
                        </div>
                    </template>
                </Column>

                <Column field="address" :header="t('stores.address')">
                    <template #body="{ data }">
                        <div class="text-sm">
                            <p>{{ data.street }} {{ data.street_number }}</p>
                            <p class="text-gray-500">{{ data.postal_code }} {{ getLocalizedName(data.city?.name) }}</p>
                        </div>
                    </template>
                </Column>

                <Column field="canton" :header="t('stores.canton')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.canton?.code || '—' }}</span>
                    </template>
                </Column>

                <Column field="format" :header="t('stores.format')">
                    <template #body="{ data }">
                        <div class="flex items-center gap-1">
                            <span class="text-sm">{{ data.format || '—' }}</span>
                            <Tag v-if="data.is_xxl" value="XXL" severity="warn" class="text-xs" />
                        </div>
                    </template>
                </Column>

                <Column field="phone" :header="t('stores.phone')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.phone || '—' }}</span>
                    </template>
                </Column>

                <Column field="is_active" :header="t('stores.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_active ? t('stores.active') : t('stores.inactive')"
                            :severity="data.is_active ? 'success' : 'danger'"
                        />
                    </template>
                </Column>

                <Column :header="t('common.actions')" style="width: 100px">
                    <template #body="{ data }">
                        <Button
                            icon="pi pi-eye"
                            severity="info"
                            text
                            rounded
                            size="small"
                            @click="router.get(route('admin.stores.show', { id: data._id }))"
                            data-testid="view-btn"
                        />
                    </template>
                </Column>

                <template #empty>
                    <div class="text-center py-12">
                        <i class="pi pi-building text-4xl text-gray-300 mb-3" />
                        <p class="text-gray-500 font-medium">{{ t('stores.noStores') }}</p>
                        <p class="text-gray-400 text-sm">{{ t('stores.noStoresDescription') }}</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>
