<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { useConfirm } from 'primevue/useconfirm';

interface ProductItem {
    _id: string;
    name: string;
    name_short: string | null;
    slug: string;
    code: string;
    price: number;
    original_price: number | null;
    currency: string;
    images: Array<{ alt: string; src: { xs?: string; sm?: string; md?: string } }>;
    rating: number;
    review_count: number;
    availability_state: string;
    is_active: boolean;
    status: string;
    brand?: { _id: string; name: string };
    category?: { _id: string; name: Record<string, string> };
    created_at: string;
}

interface StatusOption {
    value: string;
    label: string;
}

interface BrandOption {
    _id: string;
    name: string;
}

interface CategoryOption {
    _id: string;
    name: Record<string, string>;
    level: number;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    products: {
        data: ProductItem[];
        meta: PaginationMeta;
    };
    brands: { data: BrandOption[] };
    categories: { data: CategoryOption[] };
    filters: Record<string, string>;
    statuses: StatusOption[];
}

const props = defineProps<Props>();
const { t, locale } = useI18n();
const confirm = useConfirm();

const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || null);
const selectedBrand = ref(props.filters?.brand_id ? Number(props.filters.brand_id) : null);
const selectedCategory = ref(props.filters?.category_id ? Number(props.filters.category_id) : null);

let searchTimeout: ReturnType<typeof setTimeout>;

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (selectedStatus.value) params.status = selectedStatus.value;
    if (selectedBrand.value) params.brand_id = selectedBrand.value;
    if (selectedCategory.value) params.category_id = selectedCategory.value;

    router.get(route('admin.products.index'), params, {
        preserveState: true,
        preserveScroll: true,
    });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([selectedStatus, selectedBrand, selectedCategory], applyFilters);

function resetFilters() {
    search.value = '';
    selectedStatus.value = null;
    selectedBrand.value = null;
    selectedCategory.value = null;
    router.get(route('admin.products.index'), {}, { preserveState: true });
}

function onPage(event: { page: number; rows: number }) {
    const params: Record<string, any> = { ...props.filters, page: event.page + 1, per_page: event.rows };
    router.get(route('admin.products.index'), params, { preserveState: true });
}

function getStatusSeverity(status: string): string {
    const map: Record<string, string> = {
        PUBLISHED: 'success',
        DRAFT: 'warn',
        INACTIVE: 'danger',
        ARCHIVED: 'secondary',
    };
    return map[status] || 'info';
}

function getStatusLabel(status: string): string {
    const key = `products.${status === 'INACTIVE' ? 'inactive' : status.toLowerCase()}`;
    return t(key, status);
}

function getProductImage(product: ProductItem): string | null {
    if (product.images && product.images.length > 0) {
        const img = product.images[0];
        return img.src?.sm || img.src?.xs || img.src?.md || null;
    }
    return null;
}

function getCategoryName(category: { name: Record<string, string> } | undefined): string {
    if (!category?.name) return '—';
    return category.name[locale.value] || category.name['de'] || Object.values(category.name)[0] || '—';
}

function formatPrice(price: number, currency: string): string {
    return `${currency} ${Number(price).toLocaleString('de-CH', { minimumFractionDigits: 2 })}`;
}

function viewProduct(id: string) {
    router.get(route('admin.products.show', { id }));
}

function editProduct(id: string) {
    router.get(route('admin.products.edit', { id }));
}

function deleteProduct(id: string) {
    confirm.require({
        message: t('products.confirmDeleteMessage'),
        header: t('products.confirmDelete'),
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.products.destroy', { id }));
        },
    });
}
</script>

<template>
    <AdminLayout :title="t('products.title')">
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900" data-testid="products-title">{{ t('products.title') }}</h1>
                <p class="text-gray-500 mt-1" data-testid="products-count">
                    {{ products.meta?.total || 0 }} {{ t('products.title').toLowerCase() }}
                </p>
            </div>
            <Button
                :label="t('products.addProduct')"
                icon="pi pi-plus"
                @click="router.get(route('admin.products.create'))"
                data-testid="add-product-btn"
            />
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6" data-testid="product-filters">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <InputText
                    v-model="search"
                    :placeholder="t('products.searchPlaceholder')"
                    class="w-full"
                    data-testid="search-input"
                />
                <Select
                    v-model="selectedStatus"
                    :options="[{ value: null, label: t('products.allStatuses') }, ...statuses]"
                    option-label="label"
                    option-value="value"
                    :placeholder="t('products.status')"
                    class="w-full"
                    data-testid="status-filter"
                />
                <Select
                    v-model="selectedBrand"
                    :options="[{ _id: null, name: t('products.allBrands') }, ...brands.data]"
                    option-label="name"
                    option-value="_id"
                    :placeholder="t('products.brand')"
                    class="w-full"
                    data-testid="brand-filter"
                />
                <Select
                    v-model="selectedCategory"
                    :options="[{ _id: null, name: { [locale]: t('products.allCategories') } }, ...categories.data]"
                    :option-label="(cat: any) => getCategoryName(cat)"
                    option-value="_id"
                    :placeholder="t('products.category')"
                    class="w-full"
                    data-testid="category-filter"
                />
                <Button
                    :label="t('common.reset')"
                    icon="pi pi-times"
                    severity="secondary"
                    outlined
                    @click="resetFilters"
                    data-testid="reset-filters-btn"
                />
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" data-testid="products-table">
            <DataTable
                :value="products.data"
                :paginator="true"
                :rows="products.meta?.per_page || 20"
                :total-records="products.meta?.total || 0"
                :lazy="true"
                :first="((products.meta?.current_page || 1) - 1) * (products.meta?.per_page || 20)"
                @page="onPage"
                :rows-per-page-options="[10, 20, 50]"
                responsive-layout="scroll"
                striped-rows
                data-testid="products-datatable"
            >
                <template #empty>
                    <div class="text-center py-12">
                        <i class="pi pi-box text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">{{ t('products.noProducts') }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ t('products.noProductsDescription') }}</p>
                    </div>
                </template>

                <Column header="" style="width: 4rem">
                    <template #body="{ data }">
                        <img
                            v-if="getProductImage(data)"
                            :src="getProductImage(data)!"
                            :alt="data.name"
                            class="w-10 h-10 object-cover rounded"
                        />
                        <div
                            v-else
                            class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center"
                        >
                            <i class="pi pi-image text-gray-400"></i>
                        </div>
                    </template>
                </Column>

                <Column :header="t('products.name')" field="name" sortable style="min-width: 16rem">
                    <template #body="{ data }">
                        <div>
                            <p class="font-medium text-gray-900">{{ data.name_short || data.name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ data.code }}</p>
                        </div>
                    </template>
                </Column>

                <Column :header="t('products.brand')" style="min-width: 8rem">
                    <template #body="{ data }">
                        {{ data.brand?.name || '—' }}
                    </template>
                </Column>

                <Column :header="t('products.category')" style="min-width: 10rem">
                    <template #body="{ data }">
                        {{ getCategoryName(data.category) }}
                    </template>
                </Column>

                <Column :header="t('products.price')" field="price" sortable style="min-width: 7rem">
                    <template #body="{ data }">
                        <span class="font-medium">{{ formatPrice(data.price, data.currency) }}</span>
                    </template>
                </Column>

                <Column :header="t('products.status')" style="min-width: 7rem">
                    <template #body="{ data }">
                        <Tag :value="getStatusLabel(data.status)" :severity="getStatusSeverity(data.status)" />
                    </template>
                </Column>

                <Column :header="t('products.rating')" style="min-width: 5rem">
                    <template #body="{ data }">
                        <span v-if="data.rating">
                            <i class="pi pi-star-fill text-yellow-400 mr-1 text-sm"></i>
                            {{ Number(data.rating).toFixed(1) }}
                        </span>
                        <span v-else class="text-gray-400">—</span>
                    </template>
                </Column>

                <Column :header="t('products.actions')" style="min-width: 10rem">
                    <template #body="{ data }">
                        <div class="flex gap-1">
                            <Button
                                icon="pi pi-eye"
                                severity="info"
                                text
                                rounded
                                @click="viewProduct(data._id)"
                                v-tooltip.top="t('products.viewProduct')"
                                data-testid="view-btn"
                            />
                            <Button
                                icon="pi pi-pencil"
                                severity="success"
                                text
                                rounded
                                @click="editProduct(data._id)"
                                v-tooltip.top="t('common.edit')"
                                data-testid="edit-btn"
                            />
                            <Button
                                icon="pi pi-trash"
                                severity="danger"
                                text
                                rounded
                                @click="deleteProduct(data._id)"
                                v-tooltip.top="t('common.delete')"
                                data-testid="delete-btn"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
