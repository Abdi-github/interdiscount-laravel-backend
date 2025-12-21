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
    reviews: {
        data: Array<{
            _id: string;
            product_id: number;
            user_id: number;
            user?: { _id: string; first_name: string; last_name: string; email: string };
            product?: { _id: string; name: string; slug: string };
            rating: number;
            title: string;
            comment: string;
            language: string;
            is_verified_purchase: boolean;
            is_approved: boolean;
            helpful_count: number;
            created_at: string;
        }>;
        meta?: { total: number; per_page: number; current_page: number; last_page: number };
    };
    filters: Record<string, string>;
}>();

const search = ref(props.filters.search || '');
const approvalFilter = ref<string | null>(props.filters.is_approved ?? null);
const ratingFilter = ref<string | null>(props.filters.rating ?? null);

const approvalOptions = computed(() => [
    { label: t('reviews.allStatuses'), value: null },
    { label: t('reviews.approved'), value: '1' },
    { label: t('reviews.pending'), value: '0' },
]);

const ratingOptions = computed(() => [
    { label: t('reviews.allRatings'), value: null },
    { label: '⭐ 1', value: '1' },
    { label: '⭐⭐ 2', value: '2' },
    { label: '⭐⭐⭐ 3', value: '3' },
    { label: '⭐⭐⭐⭐ 4', value: '4' },
    { label: '⭐⭐⭐⭐⭐ 5', value: '5' },
]);

function applyFilters() {
    const params: Record<string, string> = {};
    if (search.value) params.search = search.value;
    if (approvalFilter.value !== null) params.is_approved = approvalFilter.value;
    if (ratingFilter.value !== null) params.rating = ratingFilter.value;
    router.get(route('admin.reviews.index'), params, { preserveState: true, replace: true });
}

function resetFilters() {
    search.value = '';
    approvalFilter.value = null;
    ratingFilter.value = null;
    router.get(route('admin.reviews.index'), {}, { preserveState: true, replace: true });
}

let searchTimeout: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});
watch(approvalFilter, applyFilters);
watch(ratingFilter, applyFilters);

function approveReview(id: string) {
    router.put(route('admin.reviews.approve', id), {}, { preserveState: true });
}

function deleteReview(id: string) {
    if (confirm(t('reviews.confirmDelete'))) {
        router.delete(route('admin.reviews.destroy', id), { preserveState: true });
    }
}

function formatDate(dateStr: string): string {
    const d = new Date(dateStr);
    return locale.value === 'de'
        ? d.toLocaleDateString('de-CH')
        : d.toLocaleDateString('en-US');
}

function renderStars(rating: number): string {
    return '★'.repeat(rating) + '☆'.repeat(5 - rating);
}
</script>

<template>
    <AdminLayout :title="t('reviews.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900">{{ t('reviews.title') }}</h1>
            <p class="text-surface-500 mt-1">{{ reviews.meta?.total ?? reviews.data.length }} {{ t('reviews.title').toLowerCase() }}</p>
        </div>

        <div class="flex flex-wrap gap-3 mb-4">
            <InputText
                v-model="search"
                :placeholder="t('reviews.searchPlaceholder')"
                class="w-64"
            />
            <Select
                v-model="approvalFilter"
                :options="approvalOptions"
                optionLabel="label"
                optionValue="value"
                :placeholder="t('reviews.allStatuses')"
                class="w-48"
            />
            <Select
                v-model="ratingFilter"
                :options="ratingOptions"
                optionLabel="label"
                optionValue="value"
                :placeholder="t('reviews.allRatings')"
                class="w-48"
            />
            <Button
                :label="t('common.reset')"
                icon="pi pi-refresh"
                severity="secondary"
                outlined
                @click="resetFilters"
            />
        </div>

        <DataTable
            :value="reviews.data"
            :paginator="true"
            :rows="reviews.meta?.per_page || 20"
            :totalRecords="reviews.meta?.total"
            :lazy="true"
            dataKey="_id"
            stripedRows
            @page="(e: any) => router.get(route('admin.reviews.index'), { ...props.filters, page: e.page + 1 }, { preserveState: true, replace: true })"
        >
            <Column :header="t('reviews.product')" field="product.name">
                <template #body="{ data }">
                    <span class="font-medium">{{ data.product?.name || '—' }}</span>
                </template>
            </Column>
            <Column :header="t('reviews.customer')" field="user">
                <template #body="{ data }">
                    <span v-if="data.user">{{ data.user.first_name }} {{ data.user.last_name }}</span>
                    <span v-else>—</span>
                </template>
            </Column>
            <Column :header="t('reviews.rating')" field="rating">
                <template #body="{ data }">
                    <span class="text-amber-500 text-lg tracking-tight">{{ renderStars(data.rating) }}</span>
                </template>
            </Column>
            <Column :header="t('reviews.title_field')" field="title">
                <template #body="{ data }">
                    <span>{{ data.title || '—' }}</span>
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
            <Column :header="t('reviews.date')" field="created_at">
                <template #body="{ data }">
                    <span>{{ formatDate(data.created_at) }}</span>
                </template>
            </Column>
            <Column :header="t('common.actions')">
                <template #body="{ data }">
                    <div class="flex gap-2">
                        <Button
                            v-if="!data.is_approved"
                            icon="pi pi-check"
                            severity="success"
                            text
                            rounded
                            :title="t('reviews.approve')"
                            @click="approveReview(data._id)"
                        />
                        <Button
                            icon="pi pi-trash"
                            severity="danger"
                            text
                            rounded
                            :title="t('common.delete')"
                            @click="deleteReview(data._id)"
                        />
                    </div>
                </template>
            </Column>
        </DataTable>
    </AdminLayout>
</template>
