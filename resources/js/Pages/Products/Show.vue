<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import { useConfirm } from 'primevue/useconfirm';

interface ProductData {
    _id: string;
    name: string;
    name_short: string | null;
    slug: string;
    code: string;
    displayed_code: string | null;
    price: number;
    original_price: number | null;
    currency: string;
    images: Array<{ alt: string; src: { xs?: string; sm?: string; md?: string } }>;
    rating: number;
    review_count: number;
    specification: string | null;
    availability_state: string;
    delivery_days: number | null;
    in_store_possible: boolean;
    release_date: string | null;
    services: Array<{ code: string; name: string; price: number }>;
    promo_labels: string[];
    is_speed_product: boolean;
    is_orderable: boolean;
    is_sustainable: boolean;
    is_active: boolean;
    status: string;
    brand?: { _id: string; name: string };
    category?: { _id: string; name: Record<string, string> };
    reviews?: Array<{
        _id: string;
        rating: number;
        title: string;
        comment: string;
        is_approved: boolean;
        user?: { first_name: string; last_name: string };
        created_at: string;
    }>;
    created_at: string;
    updated_at: string;
}

interface Props {
    product: { data: ProductData };
}

const props = defineProps<Props>();
const { t, locale } = useI18n();
const confirm = useConfirm();

const product = props.product.data;

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

function getCategoryName(category: { name: Record<string, string> } | undefined): string {
    if (!category?.name) return '—';
    return category.name[locale.value] || category.name['de'] || Object.values(category.name)[0] || '—';
}

function formatPrice(price: number, currency: string): string {
    return `${currency} ${Number(price).toLocaleString('de-CH', { minimumFractionDigits: 2 })}`;
}

function deleteProduct() {
    confirm.require({
        message: t('products.confirmDeleteMessage'),
        header: t('products.confirmDelete'),
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            router.delete(route('admin.products.destroy', { id: product._id }));
        },
    });
}
</script>

<template>
    <AdminLayout :title="product.name_short || product.name">
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <Button
                    :label="t('products.backToProducts')"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    text
                    @click="router.get(route('admin.products.index'))"
                    class="mb-2"
                />
                <h1 class="text-2xl font-bold text-gray-900" data-testid="product-name">
                    {{ product.name_short || product.name }}
                </h1>
                <p class="text-gray-500 mt-1">{{ product.code }}</p>
            </div>
            <div class="flex gap-2">
                <Button
                    :label="t('common.edit')"
                    icon="pi pi-pencil"
                    @click="router.get(route('admin.products.edit', { id: product._id }))"
                    data-testid="edit-product-btn"
                />
                <Button
                    :label="t('common.delete')"
                    icon="pi pi-trash"
                    severity="danger"
                    outlined
                    @click="deleteProduct"
                    data-testid="delete-product-btn"
                />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Images -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-images">
                    <h3 class="text-lg font-semibold mb-4">{{ t('products.image') }}</h3>
                    <div v-if="product.images && product.images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div v-for="(img, idx) in product.images" :key="idx" class="aspect-square bg-gray-50 rounded-lg overflow-hidden">
                            <img
                                :src="img.src?.md || img.src?.sm || img.src?.xs || ''"
                                :alt="img.alt || product.name"
                                class="w-full h-full object-contain"
                            />
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400">
                        <i class="pi pi-image text-4xl mb-2"></i>
                        <p>No images available</p>
                    </div>
                </div>

                <!-- Specification -->
                <div v-if="product.specification" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-spec">
                    <h3 class="text-lg font-semibold mb-4">{{ t('products.specification') }}</h3>
                    <div class="prose prose-sm max-w-none text-gray-700" v-html="product.specification"></div>
                </div>

                <!-- Reviews -->
                <div v-if="product.reviews && product.reviews.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-reviews">
                    <h3 class="text-lg font-semibold mb-4">{{ t('products.reviews') }} ({{ product.review_count }})</h3>
                    <div class="space-y-4">
                        <div v-for="review in product.reviews" :key="review._id" class="border-b border-gray-100 pb-4 last:border-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="flex">
                                    <i v-for="s in 5" :key="s" class="pi text-sm" :class="s <= review.rating ? 'pi-star-fill text-yellow-400' : 'pi-star text-gray-300'"></i>
                                </span>
                                <span class="text-sm font-medium">{{ review.title }}</span>
                                <Tag v-if="!review.is_approved" value="Pending" severity="warn" class="ml-auto" />
                            </div>
                            <p class="text-sm text-gray-600">{{ review.comment }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ review.user ? `${review.user.first_name} ${review.user.last_name}` : 'Anonymous' }}
                                · {{ new Date(review.created_at).toLocaleDateString() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-info">
                    <h3 class="text-lg font-semibold mb-4">{{ t('products.productDetails') }}</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.status') }}</span>
                            <Tag :value="getStatusLabel(product.status)" :severity="getStatusSeverity(product.status)" />
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.price') }}</span>
                            <span class="font-semibold">{{ formatPrice(product.price, product.currency) }}</span>
                        </div>
                        <div v-if="product.original_price" class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.originalPrice') }}</span>
                            <span class="line-through text-gray-400">{{ formatPrice(product.original_price, product.currency) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.brand') }}</span>
                            <span>{{ product.brand?.name || '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.category') }}</span>
                            <span>{{ getCategoryName(product.category) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.rating') }}</span>
                            <span v-if="product.rating">
                                <i class="pi pi-star-fill text-yellow-400 mr-1"></i>
                                {{ Number(product.rating).toFixed(1) }} ({{ product.review_count }})
                            </span>
                            <span v-else class="text-gray-400">—</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.availability') }}</span>
                            <span>{{ t(`products.${product.availability_state}`, product.availability_state) }}</span>
                        </div>
                        <div v-if="product.delivery_days" class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.deliveryDays') }}</span>
                            <span>{{ product.delivery_days }}</span>
                        </div>
                    </div>
                </div>

                <!-- Flags -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-flags">
                    <h3 class="text-lg font-semibold mb-4">Flags</h3>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i :class="product.is_active ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"></i>
                            <span>{{ t('products.active') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i :class="product.is_orderable ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"></i>
                            <span>{{ t('products.orderable') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i :class="product.in_store_possible ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"></i>
                            <span>{{ t('products.inStorePossible') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i :class="product.is_speed_product ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"></i>
                            <span>{{ t('products.speedProduct') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i :class="product.is_sustainable ? 'pi pi-check-circle text-green-500' : 'pi pi-times-circle text-red-500'"></i>
                            <span>{{ t('products.sustainable') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Services -->
                <div v-if="product.services && product.services.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="product-services">
                    <h3 class="text-lg font-semibold mb-4">Services</h3>
                    <div class="space-y-2">
                        <div v-for="service in product.services" :key="service.code" class="flex justify-between text-sm">
                            <span>{{ service.name }}</span>
                            <span class="font-medium">{{ formatPrice(service.price, product.currency) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold mb-4">Meta</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.slug') }}</span>
                            <span class="text-gray-700 font-mono text-xs">{{ product.slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.code') }}</span>
                            <span class="font-mono text-xs">{{ product.code }}</span>
                        </div>
                        <div v-if="product.displayed_code" class="flex justify-between">
                            <span class="text-gray-500">{{ t('products.displayedCode') }}</span>
                            <span class="font-mono text-xs">{{ product.displayed_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created</span>
                            <span>{{ new Date(product.created_at).toLocaleDateString() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Updated</span>
                            <span>{{ new Date(product.updated_at).toLocaleDateString() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
