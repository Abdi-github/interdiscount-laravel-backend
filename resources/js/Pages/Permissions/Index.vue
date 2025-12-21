<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';

const { t, locale } = useI18n();

const props = defineProps<{
    permissions: {
        data: Array<{
            _id: string;
            name: string;
            display_name: Record<string, string>;
            description: Record<string, string>;
            resource: string;
            action: string;
            is_active: boolean;
        }>;
    };
}>();

const search = ref('');
const selectedResource = ref<string | null>(null);

const getTranslation = (translations: Record<string, string>) => {
    return translations?.[locale.value] || translations?.de || '';
};

const resources = computed(() => {
    const set = new Set(props.permissions.data.map(p => p.resource));
    return Array.from(set).sort();
});

const resourceOptions = computed(() => [
    { label: t('permissions.allResources'), value: null },
    ...resources.value.map(r => ({ label: r, value: r })),
]);

const filteredPermissions = computed(() => {
    let result = props.permissions.data;
    if (selectedResource.value) {
        result = result.filter(p => p.resource === selectedResource.value);
    }
    if (search.value) {
        const q = search.value.toLowerCase();
        result = result.filter(p =>
            p.name.toLowerCase().includes(q) ||
            getTranslation(p.display_name).toLowerCase().includes(q) ||
            p.resource.toLowerCase().includes(q) ||
            p.action.toLowerCase().includes(q)
        );
    }
    return result;
});

const actionSeverity = (action: string): string => {
    const map: Record<string, string> = {
        read: 'info',
        create: 'success',
        update: 'warn',
        delete: 'danger',
        '*': 'danger',
    };
    return map[action] || 'secondary';
};
</script>

<template>
    <AdminLayout :title="t('permissions.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">{{ t('permissions.title') }}</h1>
            <p class="text-surface-500 mt-1">{{ filteredPermissions.length }} {{ t('permissions.title') }}</p>
        </div>

        <div class="card">
            <div class="flex flex-wrap gap-3 mb-4">
                <InputText
                    v-model="search"
                    :placeholder="t('permissions.searchPlaceholder')"
                    class="w-64"
                />
                <Select
                    v-model="selectedResource"
                    :options="resourceOptions"
                    optionLabel="label"
                    optionValue="value"
                    :placeholder="t('permissions.allResources')"
                    class="w-48"
                />
            </div>

            <DataTable
                :value="filteredPermissions"
                stripedRows
                :paginator="filteredPermissions.length > 20"
                :rows="20"
                tableStyle="min-width: 50rem"
            >
                <Column :header="t('permissions.displayName')" sortable sortField="name">
                    <template #body="{ data }">
                        <div>
                            <div class="font-semibold">{{ getTranslation(data.display_name) }}</div>
                            <div class="text-xs text-surface-400">{{ data.name }}</div>
                        </div>
                    </template>
                </Column>

                <Column :header="t('permissions.resource')" field="resource" sortable>
                    <template #body="{ data }">
                        <span class="font-mono text-sm capitalize">{{ data.resource }}</span>
                    </template>
                </Column>

                <Column :header="t('permissions.action')" field="action" sortable>
                    <template #body="{ data }">
                        <Tag
                            :value="data.action"
                            :severity="actionSeverity(data.action)"
                        />
                    </template>
                </Column>

                <Column :header="t('permissions.description')">
                    <template #body="{ data }">
                        <span class="text-sm text-surface-600">{{ getTranslation(data.description) }}</span>
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
            </DataTable>
        </div>
    </AdminLayout>
</template>
