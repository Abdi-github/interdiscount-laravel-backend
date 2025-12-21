<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

const { t, locale } = useI18n();

const props = defineProps<{
    cantons: {
        data: Array<{
            _id: string;
            name: Record<string, string>;
            code: string;
            slug: string;
            is_active: boolean;
        }>;
    };
    cities: {
        data: Array<{
            _id: string;
            name: Record<string, string>;
            slug: string;
            canton_id: number;
            canton?: { _id: string; name: Record<string, string>; code: string };
            postal_codes: string[];
            is_active: boolean;
        }>;
    };
    filters: Record<string, string>;
}>();

const activeTab = ref('0');
const cantonSearch = ref('');
const citySearch = ref('');

const filteredCantons = computed(() => {
    if (!cantonSearch.value) return props.cantons.data;
    const q = cantonSearch.value.toLowerCase();
    return props.cantons.data.filter(c =>
        (c.name[locale.value] || c.name.de || '').toLowerCase().includes(q) ||
        c.code.toLowerCase().includes(q)
    );
});

const filteredCities = computed(() => {
    if (!citySearch.value) return props.cities.data;
    const q = citySearch.value.toLowerCase();
    return props.cities.data.filter(c =>
        (c.name[locale.value] || c.name.de || '').toLowerCase().includes(q) ||
        c.slug.toLowerCase().includes(q) ||
        (c.postal_codes || []).some(pc => pc.includes(q))
    );
});
</script>

<template>
    <AdminLayout :title="t('locations.title')">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-surface-900">{{ t('locations.title') }}</h1>
            <p class="text-surface-500 mt-1">{{ cantons.data.length }} {{ t('locations.cantons') }} · {{ cities.data.length }} {{ t('locations.cities') }}</p>
        </div>

        <Tabs v-model:value="activeTab">
            <TabList>
                <Tab value="0">{{ t('locations.cantons') }} ({{ cantons.data.length }})</Tab>
                <Tab value="1">{{ t('locations.cities') }} ({{ cities.data.length }})</Tab>
            </TabList>
            <TabPanels>
                <TabPanel value="0">
                    <div class="flex gap-3 mb-4 mt-4">
                        <InputText
                            v-model="cantonSearch"
                            :placeholder="t('locations.searchCantons')"
                            class="w-64"
                        />
                    </div>
                    <DataTable
                        :value="filteredCantons"
                        :paginator="true"
                        :rows="30"
                        dataKey="_id"
                        stripedRows
                    >
                        <Column :header="t('common.name')">
                            <template #body="{ data }">
                                <span class="font-medium">{{ data.name[locale] || data.name.de }}</span>
                            </template>
                        </Column>
                        <Column :header="t('locations.code')" field="code" />
                        <Column :header="t('locations.slug')" field="slug" />
                        <Column :header="t('common.status')">
                            <template #body="{ data }">
                                <Tag
                                    :value="data.is_active ? t('common.active') : t('common.inactive')"
                                    :severity="data.is_active ? 'success' : 'danger'"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
                <TabPanel value="1">
                    <div class="flex gap-3 mb-4 mt-4">
                        <InputText
                            v-model="citySearch"
                            :placeholder="t('locations.searchCities')"
                            class="w-64"
                        />
                    </div>
                    <DataTable
                        :value="filteredCities"
                        :paginator="true"
                        :rows="30"
                        dataKey="_id"
                        stripedRows
                    >
                        <Column :header="t('common.name')">
                            <template #body="{ data }">
                                <span class="font-medium">{{ data.name[locale] || data.name.de }}</span>
                            </template>
                        </Column>
                        <Column :header="t('locations.canton')">
                            <template #body="{ data }">
                                <span v-if="data.canton">{{ data.canton.name[locale] || data.canton.name.de }} ({{ data.canton.code }})</span>
                                <span v-else>—</span>
                            </template>
                        </Column>
                        <Column :header="t('locations.postalCodes')">
                            <template #body="{ data }">
                                <span>{{ (data.postal_codes || []).join(', ') }}</span>
                            </template>
                        </Column>
                        <Column :header="t('locations.slug')" field="slug" />
                        <Column :header="t('common.status')">
                            <template #body="{ data }">
                                <Tag
                                    :value="data.is_active ? t('common.active') : t('common.inactive')"
                                    :severity="data.is_active ? 'success' : 'danger'"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </TabPanel>
            </TabPanels>
        </Tabs>
    </AdminLayout>
</template>
