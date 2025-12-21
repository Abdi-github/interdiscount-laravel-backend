<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

const { t } = useI18n();

const props = defineProps<{
    brands: {
        data: Array<{
            _id: number;
            name: string;
            slug: string;
            product_count: number;
            is_active: boolean;
            created_at: string;
        }>;
        meta?: { total: number; per_page: number; current_page: number; last_page: number };
    };
    filters: Record<string, string>;
}>();

const search = ref(props.filters.search || '');

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});

function applyFilters() {
    const params: Record<string, unknown> = {};
    if (search.value) params.search = search.value;
    router.get(route('admin.brands.index'), params, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    router.get(route('admin.brands.index'), {}, { preserveState: true, replace: true });
}

function onPage(event: { page: number; rows: number }) {
    const params: Record<string, unknown> = { page: event.page + 1, per_page: event.rows };
    if (search.value) params.search = search.value;
    router.get(route('admin.brands.index'), params, { preserveState: true, replace: true });
}
</script>

<template>
    <AdminLayout :title="t('brands.title')">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">{{ t('brands.title') }}</h1>
                <p class="text-surface-500 mt-1">{{ brands.meta?.total ?? brands.data.length }} {{ t('brands.title').toLowerCase() }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <InputText
                v-model="search"
                :placeholder="t('brands.searchBrands')"
                class="w-64"
                data-testid="brands-search"
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
                :value="brands.data"
                :paginator="true"
                :rows="brands.meta?.per_page || 50"
                :totalRecords="brands.meta?.total || brands.data.length"
                :lazy="true"
                @page="onPage"
                dataKey="_id"
                :rowsPerPageOptions="[20, 50, 100]"
                stripedRows
                data-testid="brands-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-tag text-4xl text-surface-300 mb-3"></i>
                        <p class="text-surface-500 font-medium">{{ t('brands.noBrands') }}</p>
                        <p class="text-surface-400 text-sm">{{ t('brands.noBrandsDescription') }}</p>
                    </div>
                </template>

                <Column :header="t('brands.name')" field="name">
                    <template #body="{ data }">
                        <span class="font-medium">{{ data.name }}</span>
                    </template>
                </Column>
                <Column :header="t('brands.slug')" field="slug">
                    <template #body="{ data }">
                        <span class="text-surface-500 text-sm font-mono">{{ data.slug }}</span>
                    </template>
                </Column>
                <Column :header="t('brands.productsCount')" field="product_count">
                    <template #body="{ data }">
                        <Tag :value="String(data.product_count)" severity="info" />
                    </template>
                </Column>
                <Column :header="t('brands.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_active ? t('brands.active') : t('brands.inactive')"
                            :severity="data.is_active ? 'success' : 'danger'"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
