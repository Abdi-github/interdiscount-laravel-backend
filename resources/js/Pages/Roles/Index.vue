<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

const { t, locale } = useI18n();

const props = defineProps<{
    roles: {
        data: Array<{
            _id: string;
            name: string;
            display_name: Record<string, string>;
            description: Record<string, string>;
            is_system: boolean;
            is_active: boolean;
            permissions: Array<{
                _id: string;
                name: string;
                display_name: Record<string, string>;
                resource: string;
                action: string;
            }>;
        }>;
    };
}>();

const getTranslation = (translations: Record<string, string>) => {
    return translations?.[locale.value] || translations?.de || '';
};

const viewRole = (role: any) => {
    router.get(`/admin/roles/${role._id}`);
};
</script>

<template>
    <AdminLayout :title="t('roles.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">{{ t('roles.title') }}</h1>
            <p class="text-surface-500 mt-1">{{ roles.data.length }} {{ t('roles.title') }}</p>
        </div>

        <div class="card">
            <DataTable
                :value="roles.data"
                stripedRows
                :paginator="false"
                tableStyle="min-width: 50rem"
            >
                <Column :header="t('roles.displayName')" sortable sortField="name">
                    <template #body="{ data }">
                        <div>
                            <div class="font-semibold">{{ getTranslation(data.display_name) }}</div>
                            <div class="text-xs text-surface-400">{{ data.name }}</div>
                        </div>
                    </template>
                </Column>

                <Column :header="t('roles.description')">
                    <template #body="{ data }">
                        <span class="text-sm text-surface-600">{{ getTranslation(data.description) }}</span>
                    </template>
                </Column>

                <Column :header="t('roles.permissions')">
                    <template #body="{ data }">
                        <Tag
                            v-if="data.name === 'super_admin'"
                            :value="t('roles.allPermissions')"
                            severity="danger"
                        />
                        <span v-else class="font-semibold">{{ data.permissions?.length || 0 }}</span>
                    </template>
                </Column>

                <Column :header="t('roles.system')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_system ? t('common.yes') : t('common.no')"
                            :severity="data.is_system ? 'info' : 'secondary'"
                        />
                    </template>
                </Column>

                <Column :header="t('common.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_active ? t('common.active') : t('common.inactive')"
                            :severity="data.is_active ? 'success' : 'danger'"
                        />
                    </template>
                </Column>

                <Column :header="t('common.actions')">
                    <template #body="{ data }">
                        <Button
                            icon="pi pi-eye"
                            severity="info"
                            text
                            rounded
                            :aria-label="t('common.view')"
                            @click="viewRole(data)"
                        />
                    </template>
                </Column>
            </DataTable>
        </div>
    </AdminLayout>
</template>
