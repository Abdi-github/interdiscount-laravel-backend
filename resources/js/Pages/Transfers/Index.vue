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

interface TransferItem {
    product_id: number;
    product_name: string;
    quantity: number;
    received_quantity: number;
}

interface TransferRecord {
    _id: string;
    transfer_number: string;
    from_store_id: number;
    from_store?: { _id: string; name: string };
    to_store_id: number;
    to_store?: { _id: string; name: string };
    initiated_by: number;
    initiator?: { first_name: string; last_name: string };
    status: string;
    items: TransferItem[];
    notes: string | null;
    approved_by: number | null;
    shipped_at: string | null;
    received_at: string | null;
    created_at: string;
}

interface TransferCollection {
    data: TransferRecord[];
    meta: { total: number; per_page: number; current_page: number; last_page: number };
}

const props = defineProps<{
    transfers: TransferCollection;
    stores: Store[];
    filters: {
        search?: string;
        store_id?: string;
        status?: string;
    };
}>();

const { t, d } = useI18n();

const search = ref(props.filters.search || '');
const storeFilter = ref(props.filters.store_id ? Number(props.filters.store_id) : null);
const statusFilter = ref(props.filters.status || null);

const statusOptions = [
    { label: t('common.all'), value: null },
    { label: t('transfers.statusRequested'), value: 'requested' },
    { label: t('transfers.statusApproved'), value: 'approved' },
    { label: t('transfers.statusRejected'), value: 'rejected' },
    { label: t('transfers.statusShipped'), value: 'shipped' },
    { label: t('transfers.statusReceived'), value: 'received' },
    { label: t('transfers.statusCancelled'), value: 'cancelled' },
];

const storeOptions = [
    { label: t('transfers.allStores'), value: null },
    ...props.stores.map((s) => ({ label: s.name, value: s.id })),
];

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (storeFilter.value) params.store_id = storeFilter.value;
    if (statusFilter.value) params.status = statusFilter.value;
    router.get('/admin/transfers', params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([storeFilter, statusFilter], applyFilters);

function statusSeverity(status: string): string {
    const map: Record<string, string> = {
        requested: 'warn',
        approved: 'info',
        rejected: 'danger',
        shipped: 'contrast',
        received: 'success',
        cancelled: 'secondary',
    };
    return map[status] || 'secondary';
}

function statusLabel(status: string): string {
    const map: Record<string, string> = {
        requested: t('transfers.statusRequested'),
        approved: t('transfers.statusApproved'),
        rejected: t('transfers.statusRejected'),
        shipped: t('transfers.statusShipped'),
        received: t('transfers.statusReceived'),
        cancelled: t('transfers.statusCancelled'),
    };
    return map[status] || status;
}

function formatDate(date: string | null): string {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('de-CH');
}

function totalItems(items: TransferItem[]): number {
    if (!items || !Array.isArray(items)) return 0;
    return items.reduce((sum, item) => sum + item.quantity, 0);
}
</script>

<template>
    <AdminLayout :title="t('transfers.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ t('transfers.title') }}</h1>
            <p class="text-gray-500 mt-1">{{ t('transfers.subtitle') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <InputText
                        v-model="search"
                        :placeholder="t('transfers.searchPlaceholder')"
                        class="w-full"
                        data-testid="search-input"
                    />
                </div>
                <Select
                    v-model="storeFilter"
                    :options="storeOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('transfers.allStores')"
                    class="w-48"
                    data-testid="store-filter"
                />
                <Select
                    v-model="statusFilter"
                    :options="statusOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('common.all')"
                    class="w-48"
                    data-testid="status-filter"
                />
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <DataTable
                :value="transfers.data"
                :rows="20"
                stripedRows
                :paginator="transfers.meta.last_page > 1"
                :totalRecords="transfers.meta.total"
                data-testid="transfers-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-arrows-h text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">{{ t('transfers.noTransfers') }}</p>
                    </div>
                </template>

                <Column :header="t('transfers.transferNumber')" style="width: 160px">
                    <template #body="{ data }">
                        <span class="font-mono text-sm font-medium">{{ data.transfer_number }}</span>
                    </template>
                </Column>

                <Column :header="t('transfers.fromStore')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.from_store?.name || '—' }}</span>
                    </template>
                </Column>

                <Column :header="t('transfers.toStore')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.to_store?.name || '—' }}</span>
                    </template>
                </Column>

                <Column :header="t('transfers.items')" style="width: 80px">
                    <template #body="{ data }">
                        <span class="text-sm">{{ totalItems(data.items) }}</span>
                    </template>
                </Column>

                <Column :header="t('transfers.status')" style="width: 130px">
                    <template #body="{ data }">
                        <Tag :value="statusLabel(data.status)" :severity="statusSeverity(data.status) as any" />
                    </template>
                </Column>

                <Column :header="t('transfers.initiator')">
                    <template #body="{ data }">
                        <span class="text-sm" v-if="data.initiator">
                            {{ data.initiator.first_name }} {{ data.initiator.last_name }}
                        </span>
                        <span v-else class="text-gray-400">—</span>
                    </template>
                </Column>

                <Column :header="t('transfers.date')" style="width: 120px">
                    <template #body="{ data }">
                        <span class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</span>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
