<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <AdminLayout :title="t('nav.coupons')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ t('nav.coupons') }}</h1>
            <p class="text-gray-500 mt-1">Coming soon...</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <i class="pi pi-ticket text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-400">This page is under construction</p>
        </div>
    </AdminLayout>
</template>
