<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Tag from 'primevue/tag';
import { useI18n } from 'vue-i18n';

interface AdminInfo {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    admin_type: string;
    avatar_url: string | null;
    last_login_at: string | null;
    created_at: string;
}

interface AppInfo {
    name: string;
    version: string;
    php_version: string;
    locale: string;
    supported_locales: string[];
    environment: string;
}

const props = defineProps<{
    admin: AdminInfo;
    appInfo: AppInfo;
}>();

const { t, locale } = useI18n();

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString(locale.value === 'de' ? 'de-CH' : 'en-CH', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function adminTypeLabel(type: string): string {
    const map: Record<string, string> = {
        super_admin: 'Super Admin',
        platform_admin: 'Platform Admin',
        store_manager: 'Store Manager',
        store_staff: 'Store Staff',
        support_agent: 'Support Agent',
    };
    return map[type] || type;
}

function adminTypeSeverity(type: string): string {
    const map: Record<string, string> = {
        super_admin: 'danger',
        platform_admin: 'warn',
        store_manager: 'info',
        store_staff: 'secondary',
        support_agent: 'contrast',
    };
    return map[type] || 'secondary';
}
</script>

<template>
    <AdminLayout :title="t('settings.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ t('settings.title') }}</h1>
            <p class="text-gray-500 mt-1">{{ t('settings.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="profile-card">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="pi pi-user text-blue-600"></i>
                    {{ t('settings.profile') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                        <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center text-2xl font-bold text-orange-600">
                            {{ admin.first_name.charAt(0) }}{{ admin.last_name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-gray-900">{{ admin.first_name }} {{ admin.last_name }}</p>
                            <Tag :value="adminTypeLabel(admin.admin_type)" :severity="adminTypeSeverity(admin.admin_type) as any" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('settings.email') }}</p>
                            <p class="text-sm font-medium text-gray-900">{{ admin.email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('settings.phone') }}</p>
                            <p class="text-sm font-medium text-gray-900">{{ admin.phone || '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('settings.lastLogin') }}</p>
                            <p class="text-sm font-medium text-gray-900">{{ formatDate(admin.last_login_at) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ t('settings.memberSince') }}</p>
                            <p class="text-sm font-medium text-gray-900">{{ formatDate(admin.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- App Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" data-testid="app-info-card">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="pi pi-info-circle text-green-600"></i>
                    {{ t('settings.appInfo') }}
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">{{ t('settings.appName') }}</span>
                        <span class="text-sm font-medium text-gray-900">{{ appInfo.name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">{{ t('settings.laravelVersion') }}</span>
                        <Tag :value="'v' + appInfo.version" severity="info" />
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">{{ t('settings.phpVersion') }}</span>
                        <Tag :value="appInfo.php_version" severity="secondary" />
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">{{ t('settings.environment') }}</span>
                        <Tag
                            :value="appInfo.environment"
                            :severity="appInfo.environment === 'production' ? 'danger' : 'success'"
                        />
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500">{{ t('settings.defaultLocale') }}</span>
                        <span class="text-sm font-medium text-gray-900 uppercase">{{ appInfo.locale }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-500">{{ t('settings.supportedLocales') }}</span>
                        <div class="flex gap-1">
                            <Tag
                                v-for="loc in appInfo.supported_locales"
                                :key="loc"
                                :value="loc.toUpperCase()"
                                severity="secondary"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
