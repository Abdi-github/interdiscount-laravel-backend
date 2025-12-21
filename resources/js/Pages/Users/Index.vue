<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Button from 'primevue/button';
import Tag from 'primevue/tag';

interface UserData {
    _id: string;
    email: string;
    first_name: string;
    last_name: string;
    phone: string | null;
    preferred_language: string;
    is_active: boolean;
    is_verified: boolean;
    verified_at: string | null;
    last_login_at: string | null;
    created_at: string;
}

interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    users: { data: UserData[]; meta: PaginationMeta };
    filters: Record<string, string>;
}

const props = defineProps<Props>();
const { t } = useI18n();

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.is_active ?? '');
const verifiedFilter = ref(props.filters.is_verified ?? '');

let searchTimeout: ReturnType<typeof setTimeout>;

const statusOptions = computed(() => [
    { value: '', label: t('users.allStatuses') },
    { value: '1', label: t('users.active') },
    { value: '0', label: t('users.inactive') },
]);

const verifiedOptions = computed(() => [
    { value: '', label: t('users.allVerification') },
    { value: '1', label: t('users.verified') },
    { value: '0', label: t('users.unverified') },
]);

function applyFilters() {
    const params: Record<string, any> = {};
    if (search.value) params.search = search.value;
    if (statusFilter.value !== '') params.is_active = statusFilter.value;
    if (verifiedFilter.value !== '') params.is_verified = verifiedFilter.value;
    if (props.filters.per_page) params.per_page = props.filters.per_page;

    router.get(route('admin.users.index'), params, { preserveState: true, preserveScroll: true });
}

watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});

watch([statusFilter, verifiedFilter], applyFilters);

function resetFilters() {
    search.value = '';
    statusFilter.value = '';
    verifiedFilter.value = '';
    router.get(route('admin.users.index'));
}

function onPage(event: any) {
    const params: Record<string, any> = { ...props.filters, page: event.page + 1, per_page: event.rows };
    router.get(route('admin.users.index'), params, { preserveState: true, preserveScroll: true });
}

function formatDate(dateStr: string | null): string {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('de-CH', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}

function getLanguageLabel(lang: string): string {
    const map: Record<string, string> = { de: 'DE', en: 'EN', fr: 'FR', it: 'IT' };
    return map[lang] || lang.toUpperCase();
}
</script>

<template>
    <AdminLayout :title="t('users.title')">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900" data-testid="users-title">{{ t('users.title') }}</h1>
                <p class="text-sm text-gray-500" data-testid="users-count">
                    {{ users.meta.total }} {{ t('users.title').toLowerCase() }}
                </p>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center gap-3 mb-6" data-testid="users-filters">
            <InputText
                v-model="search"
                :placeholder="t('users.searchUsers')"
                class="w-64"
                data-testid="users-search"
            />
            <Select
                v-model="statusFilter"
                :options="statusOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('users.status')"
                class="w-44"
                data-testid="users-status-filter"
            />
            <Select
                v-model="verifiedFilter"
                :options="verifiedOptions"
                option-label="label"
                option-value="value"
                :placeholder="t('users.verified')"
                class="w-48"
                data-testid="users-verified-filter"
            />
            <Button
                :label="t('common.reset')"
                icon="pi pi-filter-slash"
                severity="secondary"
                text
                @click="resetFilters"
                data-testid="reset-filters-btn"
            />
        </div>

        <!-- DataTable -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100" data-testid="users-table">
            <DataTable
                :value="users.data"
                :lazy="true"
                :paginator="true"
                :rows="users.meta.per_page"
                :totalRecords="users.meta.total"
                :first="(users.meta.current_page - 1) * users.meta.per_page"
                @page="onPage"
                :rowsPerPageOptions="[10, 20, 50]"
                stripedRows
                data-testid="users-datatable"
            >
                <Column field="name" :header="t('users.name')">
                    <template #body="{ data }">
                        <div>
                            <p class="font-medium text-sm">{{ data.first_name }} {{ data.last_name }}</p>
                            <p class="text-xs text-gray-500">{{ data.email }}</p>
                        </div>
                    </template>
                </Column>

                <Column field="phone" :header="t('users.phone')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ data.phone || '—' }}</span>
                    </template>
                </Column>

                <Column field="preferred_language" :header="t('users.language')">
                    <template #body="{ data }">
                        <Tag :value="getLanguageLabel(data.preferred_language)" severity="info" />
                    </template>
                </Column>

                <Column field="is_verified" :header="t('users.verified')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_verified ? t('users.verified') : t('users.unverified')"
                            :severity="data.is_verified ? 'success' : 'warn'"
                        />
                    </template>
                </Column>

                <Column field="is_active" :header="t('users.status')">
                    <template #body="{ data }">
                        <Tag
                            :value="data.is_active ? t('users.active') : t('users.inactive')"
                            :severity="data.is_active ? 'success' : 'danger'"
                        />
                    </template>
                </Column>

                <Column field="last_login_at" :header="t('users.lastLogin')">
                    <template #body="{ data }">
                        <span class="text-sm">{{ formatDate(data.last_login_at) }}</span>
                    </template>
                </Column>

                <Column :header="t('common.actions')" style="width: 100px">
                    <template #body="{ data }">
                        <Button
                            icon="pi pi-eye"
                            severity="info"
                            text
                            rounded
                            size="small"
                            @click="router.get(route('admin.users.show', { id: data._id }))"
                            data-testid="view-btn"
                        />
                    </template>
                </Column>

                <template #empty>
                    <div class="text-center py-12">
                        <i class="pi pi-users text-4xl text-gray-300 mb-3" />
                        <p class="text-gray-500 font-medium">{{ t('users.noUsers') }}</p>
                        <p class="text-gray-400 text-sm">{{ t('users.noUsersDescription') }}</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AdminLayout>
</template>
