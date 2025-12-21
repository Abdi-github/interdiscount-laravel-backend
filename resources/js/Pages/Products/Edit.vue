<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';

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

interface ProductData {
    _id: string;
    name: string;
    name_short: string | null;
    slug: string;
    code: string;
    displayed_code: string | null;
    brand_id: number;
    category_id: number;
    price: number;
    original_price: number | null;
    currency: string;
    specification: string | null;
    availability_state: string;
    delivery_days: number | null;
    in_store_possible: boolean;
    is_speed_product: boolean;
    is_orderable: boolean;
    is_sustainable: boolean;
    is_active: boolean;
    status: string;
}

interface Props {
    product: { data: ProductData };
    brands: { data: BrandOption[] };
    categories: { data: CategoryOption[] };
    statuses: StatusOption[];
    availabilityStates: StatusOption[];
}

const props = defineProps<Props>();
const { t, locale } = useI18n();

const p = props.product.data;

const form = useForm({
    name: p.name,
    name_short: p.name_short || '',
    slug: p.slug,
    code: p.code,
    displayed_code: p.displayed_code || '',
    brand_id: String(p.brand_id),
    category_id: String(p.category_id),
    price: p.price,
    original_price: p.original_price,
    currency: p.currency,
    specification: p.specification || '',
    availability_state: p.availability_state,
    delivery_days: p.delivery_days,
    in_store_possible: p.in_store_possible,
    is_speed_product: p.is_speed_product,
    is_orderable: p.is_orderable,
    is_sustainable: p.is_sustainable,
    is_active: p.is_active,
    status: p.status,
});

function getCategoryName(category: CategoryOption): string {
    const indent = '—'.repeat(Math.max(0, category.level - 1));
    const name = category.name[locale.value] || category.name['de'] || Object.values(category.name)[0] || '—';
    return indent ? `${indent} ${name}` : name;
}

function submit() {
    form.put(route('admin.products.update', { id: p._id }));
}
</script>

<template>
    <AdminLayout :title="t('products.editProduct')">
        <div class="mb-6">
            <Button
                :label="t('products.backToProducts')"
                icon="pi pi-arrow-left"
                severity="secondary"
                text
                @click="router.get(route('admin.products.index'))"
                class="mb-2"
            />
            <h1 class="text-2xl font-bold text-gray-900" data-testid="edit-product-title">
                {{ t('products.editProduct') }}: {{ p.name_short || p.name }}
            </h1>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.name') }} *</label>
                                <InputText
                                    v-model="form.name"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.name }"
                                    data-testid="product-name-input"
                                />
                                <small v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</small>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.nameShort') }}</label>
                                <InputText
                                    v-model="form.name_short"
                                    class="w-full"
                                    data-testid="product-name-short-input"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.slug') }} *</label>
                                    <InputText
                                        v-model="form.slug"
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.slug }"
                                        data-testid="product-slug-input"
                                    />
                                    <small v-if="form.errors.slug" class="text-red-500">{{ form.errors.slug }}</small>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.code') }} *</label>
                                    <InputText
                                        v-model="form.code"
                                        class="w-full"
                                        :class="{ 'p-invalid': form.errors.code }"
                                        data-testid="product-code-input"
                                    />
                                    <small v-if="form.errors.code" class="text-red-500">{{ form.errors.code }}</small>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.specification') }}</label>
                                <Textarea
                                    v-model="form.specification"
                                    class="w-full"
                                    rows="5"
                                    data-testid="product-spec-input"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ t('products.price') }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.price') }} *</label>
                                <InputNumber
                                    v-model="form.price"
                                    mode="currency"
                                    currency="CHF"
                                    locale="de-CH"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.price }"
                                    data-testid="product-price-input"
                                />
                                <small v-if="form.errors.price" class="text-red-500">{{ form.errors.price }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.originalPrice') }}</label>
                                <InputNumber
                                    v-model="form.original_price"
                                    mode="currency"
                                    currency="CHF"
                                    locale="de-CH"
                                    class="w-full"
                                    data-testid="product-original-price-input"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.currency') }}</label>
                                <InputText
                                    v-model="form.currency"
                                    class="w-full"
                                    disabled
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status & Categorization -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold mb-4">{{ t('products.status') }}</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.status') }}</label>
                                <Select
                                    v-model="form.status"
                                    :options="statuses"
                                    option-label="label"
                                    option-value="value"
                                    class="w-full"
                                    data-testid="product-status-select"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.brand') }} *</label>
                                <Select
                                    v-model="form.brand_id"
                                    :options="brands.data"
                                    option-label="name"
                                    option-value="_id"
                                    :placeholder="t('products.brand')"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.brand_id }"
                                    filter
                                    data-testid="product-brand-select"
                                />
                                <small v-if="form.errors.brand_id" class="text-red-500">{{ form.errors.brand_id }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.category') }} *</label>
                                <Select
                                    v-model="form.category_id"
                                    :options="categories.data"
                                    :option-label="(cat: any) => getCategoryName(cat)"
                                    option-value="_id"
                                    :placeholder="t('products.category')"
                                    class="w-full"
                                    :class="{ 'p-invalid': form.errors.category_id }"
                                    filter
                                    data-testid="product-category-select"
                                />
                                <small v-if="form.errors.category_id" class="text-red-500">{{ form.errors.category_id }}</small>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('products.availability') }}</label>
                                <Select
                                    v-model="form.availability_state"
                                    :options="availabilityStates"
                                    option-label="label"
                                    option-value="value"
                                    class="w-full"
                                    data-testid="product-availability-select"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold mb-4">Options</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">{{ t('products.active') }}</label>
                                <ToggleSwitch v-model="form.is_active" data-testid="product-active-toggle" />
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">{{ t('products.orderable') }}</label>
                                <ToggleSwitch v-model="form.is_orderable" data-testid="product-orderable-toggle" />
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">{{ t('products.inStorePossible') }}</label>
                                <ToggleSwitch v-model="form.in_store_possible" />
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">{{ t('products.speedProduct') }}</label>
                                <ToggleSwitch v-model="form.is_speed_product" />
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="text-sm text-gray-700">{{ t('products.sustainable') }}</label>
                                <ToggleSwitch v-model="form.is_sustainable" />
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <Button
                            :label="t('products.save')"
                            icon="pi pi-check"
                            type="submit"
                            :loading="form.processing"
                            class="flex-1"
                            data-testid="save-product-btn"
                        />
                        <Button
                            :label="t('products.cancel')"
                            icon="pi pi-times"
                            severity="secondary"
                            outlined
                            @click="router.get(route('admin.products.index'))"
                            class="flex-1"
                            data-testid="cancel-btn"
                        />
                    </div>
                </div>
            </div>
        </form>
    </AdminLayout>
</template>
