<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Button from 'primevue/button';

const { t } = useI18n();

const actions = [
    { label: t('products.addProduct'), icon: 'pi-plus', route: '/admin/products', severity: 'primary' as const },
    { label: t('orders.title'), icon: 'pi-shopping-cart', route: '/admin/orders', severity: 'info' as const },
    { label: t('nav.reviews'), icon: 'pi-star', route: '/admin/reviews', severity: 'warn' as const },
    { label: t('nav.inventory'), icon: 'pi-warehouse', route: '/admin/inventory', severity: 'secondary' as const },
];
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="quick-actions">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ t('dashboard.quickActions') }}</h3>
        <div class="flex flex-wrap gap-3">
            <Link v-for="action in actions" :key="action.route" :href="action.route">
                <Button
                    :label="action.label"
                    :icon="'pi ' + action.icon"
                    :severity="action.severity"
                    outlined
                    size="small"
                />
            </Link>
        </div>
    </div>
</template>
