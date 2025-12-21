<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import Button from 'primevue/button';

const { t, locale } = useI18n();

const props = defineProps<{
    coupons: {
        data: Array<{
            _id: number;
            code: string;
            description: Record<string, string>;
            discount_type: 'percentage' | 'fixed';
            discount_value: number;
            minimum_order: number | null;
            max_uses: number | null;
            used_count: number;
            valid_from: string | null;
            valid_until: string | null;
            is_active: boolean;
            created_at: string;
        }>;
        meta?: { total: number; per_page: number; current_page: number; last_page: number };
    };
    filters: Record<string, string>;
}>();

const search = ref(props.filters.search || '');
const statusFilter = ref<string | null>(props.filters.is_active ?? null);
const typeFilter = ref<string | null>(props.filters.discount_type ?? null);

const statusOptions = computed(() => [
    { label: t('coupons.allStatuses'), value: null },
    { label: t('coupons.active'), value: '1' },
    { label: t('coupons.inactive'), value: '0' },
]);

const typeOptions = computed(() => [
    { label: t('coupons.allTypes'), value: null },
    { label: t('coupons.percentage'), value: 'percentage' },
    { label: t('coupons.fixed'), value: 'fixed' },
]);

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});

function applyFilters() {
    const params: Record<string, unknown> = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value !== null) params.is_active = statusFilter.value;
    if (typeFilter.value !== null) params.discount_type = typeFilter.value;
    router.get(route('admin.coupons.index'), params, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    statusFilter.value = null;
    typeFilter.value = null;
    router.get(route('admin.coupons.index'), {}, { preserveState: true, replace: true });
}

function onPage(event: { page: number; rows: number }) {
    const params: Record<string, unknown> = { page: event.page + 1, per_page: event.rows };
    if (search.value) params.search = search.value;
    if (statusFilter.value !== null) params.is_active = statusFilter.value;
    if (typeFilter.value !== null) params.discount_type = typeFilter.value;
    router.get(route('admin.coupons.index'), params, { preserveState: true, replace: true });
}

function formatDiscount(coupon: { discount_type: string; discount_value: number }): string {
    if (coupon.discount_type === 'percentage') return `${coupon.discount_value}%`;
    return `CHF ${coupon.discount_value.toFixed(2)}`;
}

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString(locale.value === 'de' ? 'de-CH' : 'en-GB', {
        day: '2-digit', month: '2-digit', year: 'numeric',
    });
}

function getDescription(desc: Record<string, string>): string {
    return desc[locale.value] || desc['de'] || Object.values(desc)[0] || '';
}

function isExpired(coupon: { valid_until: string | null }): boolean {
    if (!coupon.valid_until) return false;
    return new Date(coupon.valid_until) < new Date();
}

function getStatusSeverity(coupon: { is_active: boolean; valid_until: string | null }): string {
    if (isExpired(coupon)) return 'warn';
    return coupon.is_active ? 'success' : 'danger';
}

function getStatusLabel(coupon: { is_active: boolean; valid_until: string | null }): string {
    if (isExpired(coupon)) return t('coupons.expired');
    return coupon.is_active ? t('coupons.active') : t('coupons.inactive');
}
</script>

<template>
    <AdminLayout :title="t('coupons.title')">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">{{ t('coupons.title') }}</h1>
                <p class="text-surface-500 mt-1">{{ coupons.meta?.total ?? coupons.data.length }} {{ t('coupons.title').toLowerCase() }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <InputText
                v-model="search"
                :placeholder="t('coupons.searchCoupons')"
                class="w-64"
                data-testid="coupons-search"
            />
            <Select
                v-model="statusFilter"
                :options="statusOptions"
                optionLabel="label"
                optionValue="value"
                :placeholder="t('coupons.allStatuses')"
                class="w-48"
                @change="applyFilters"
                data-testid="coupons-status-filter"
            />
            <Select
                v-model="typeFilter"
                :options="typeOptions"
                optionLabel="label"
                optionValue="value"
                :placeholder="t('coupons.allTypes')"
                class="w-48"
                @change="applyFilters"
                data-testid="coupons-type-filter"
            />
            <Button
                :label="t('common.reset')"
                icon="pi pi-refresh"
                severity="secondary"
                text
                @click="resetFilters"
            />
        </div>

        <div class="card">
            <DataTable
                :value="coupons.data"
                :paginator="true"
                :rows="coupons.meta?.per_page || 20"
                :totalRecords="coupons.meta?.total || coupons.data.length"
                :lazy="true"
                @page="onPage"
                dataKey="_id"
                :rowsPerPageOptions="[10, 20, 50]"
                stripedRows
                data-testid="coupons-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-ticket text-4xl text-surface-300 mb-3"></i>
                        <p class="text-surface-500 font-medium">{{ t('coupons.noCoupons') }}</p>
                        <p class="text-surface-400 text-sm">{{ t('coupons.noCouponsDescription') }}</p>
                    </div>
                </template>

                <Column :header="t('coupons.code')" field="code">
                    <template #body="{ data }">
                        <span class="font-mono font-bold">{{ data.code }}</span>
                    </template>
                </Column>
                <Column :header="t('coupons.description')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ getDescription(data.description) }}</span>
                    </template>
                </Column>
                <Column :header="t('coupons.discountType')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.discount_type === 'percentage' ? t('coupons.percentage') : t('coupons.fixed')"
                            :severity="data.discount_type === 'percentage' ? 'info' : 'warn'"
                        />
                    </template>
                </Column>
                <Column :header="t('coupons.discountValue')">
                    <template #body="{ data }">
                        <span class="font-semibold">{{ formatDiscount(data) }}</span>
                    </template>
                </Column>
                <Column :header="t('coupons.minimumOrder')">
                    <template #body="{ data }">
                        <span v-if="data.minimum_order">CHF {{ data.minimum_order.toFixed(2) }}</span>
                        <span v-else class="text-surface-400">{{ t('coupons.noMinimum') }}</span>
                    </template>
                </Column>
                <Column :header="t('coupons.usage')">
                    <template #body="{ data }">
                        <span>{{ data.used_count }} / {{ data.max_uses ?? t('coupons.unlimited') }}</span>
                    </template>
                </Column>
                <Column :header="t('coupons.validity')">
                    <template #body="{ data }">
                        <div class="text-sm">
                            <div v-if="data.valid_from">{{ formatDate(data.valid_from) }}</div>
                            <div v-if="data.valid_until">→ {{ formatDate(data.valid_until) }}</div>
                            <span v-if="!data.valid_from && !data.valid_until" class="text-surface-400">{{ t('coupons.unlimited') }}</span>
                        </div>
                    </template>
                </Column>
                <Column :header="t('coupons.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="getStatusLabel(data)"
                            :severity="getStatusSeverity(data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
