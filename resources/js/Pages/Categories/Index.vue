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
    categories: {
        data: Array<{
            _id: number;
            name: Record<string, string>;
            slug: string;
            category_id: string;
            level: number;
            parent_id: number | null;
            parent?: { _id: number; name: Record<string, string>; slug: string } | null;
            sort_order: number;
            is_active: boolean;
            products_count?: number;
            created_at: string;
        }>;
        meta?: { total: number; per_page: number; current_page: number; last_page: number };
    };
    filters: Record<string, string>;
}>();

const search = ref(props.filters.search || '');
const levelFilter = ref<number | null>(props.filters.level ? Number(props.filters.level) : null);

const levelOptions = computed(() => [
    { label: t('categories.allLevels'), value: null },
    { label: 'Level 1', value: 1 },
    { label: 'Level 2', value: 2 },
    { label: 'Level 3', value: 3 },
    { label: 'Level 4', value: 4 },
    { label: 'Level 5', value: 5 },
]);

let searchTimeout: ReturnType<typeof setTimeout>;

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 400);
});

function applyFilters() {
    const params: Record<string, unknown> = {};
    if (search.value) params.search = search.value;
    if (levelFilter.value !== null) params.level = levelFilter.value;
    router.get(route('admin.categories.index'), params, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    levelFilter.value = null;
    router.get(route('admin.categories.index'), {}, { preserveState: true, replace: true });
}

function onPage(event: { page: number; rows: number }) {
    const params: Record<string, unknown> = { page: event.page + 1, per_page: event.rows };
    if (search.value) params.search = search.value;
    if (levelFilter.value !== null) params.level = levelFilter.value;
    router.get(route('admin.categories.index'), params, { preserveState: true, replace: true });
}

function getCategoryName(name: Record<string, string>): string {
    return name[locale.value] || name['de'] || Object.values(name)[0] || '';
}
</script>

<template>
    <AdminLayout :title="t('categories.title')">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">{{ t('categories.title') }}</h1>
                <p class="text-surface-500 mt-1">{{ categories.meta?.total ?? categories.data.length }} {{ t('categories.title').toLowerCase() }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            <InputText
                v-model="search"
                :placeholder="t('categories.searchCategories')"
                class="w-64"
                data-testid="categories-search"
            />
            <Select
                v-model="levelFilter"
                :options="levelOptions"
                optionLabel="label"
                optionValue="value"
                :placeholder="t('categories.allLevels')"
                class="w-48"
                @change="applyFilters"
                data-testid="categories-level-filter"
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
                :value="categories.data"
                :paginator="true"
                :rows="categories.meta?.per_page || 50"
                :totalRecords="categories.meta?.total || categories.data.length"
                :lazy="true"
                @page="onPage"
                dataKey="_id"
                :rowsPerPageOptions="[20, 50, 100]"
                stripedRows
                data-testid="categories-table"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <i class="pi pi-folder text-4xl text-surface-300 mb-3"></i>
                        <p class="text-surface-500 font-medium">{{ t('categories.noCategories') }}</p>
                        <p class="text-surface-400 text-sm">{{ t('categories.noCategoriesDescription') }}</p>
                    </div>
                </template>

                <Column :header="t('categories.name')" field="name">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span v-if="data.level > 1" class="text-surface-400">{{ '—'.repeat(data.level - 1) }}</span>
                            <span class="font-medium">{{ getCategoryName(data.name) }}</span>
                        </div>
                    </template>
                </Column>
                <Column :header="t('categories.categoryId')" field="category_id" />
                <Column :header="t('categories.level')" field="level">
                    <template #body="{ data }">
                        <Tag :value="`Level ${data.level}`" severity="info" />
                    </template>
                </Column>
                <Column :header="t('categories.parent')" field="parent">
                    <template #body="{ data }">
                        <span v-if="data.parent">{{ getCategoryName(data.parent.name) }}</span>
                        <Tag v-else :value="t('categories.root')" severity="secondary" />
                    </template>
                </Column>
                <Column :header="t('categories.sortOrder')" field="sort_order" />
                <Column :header="t('categories.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_active ? t('categories.active') : t('categories.inactive')"
                            :severity="data.is_active ? 'success' : 'danger'"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
