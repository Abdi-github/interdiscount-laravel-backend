<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';

const { t, locale } = useI18n();

const props = defineProps<{
    role: {
        data: {
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
        };
    };
    allPermissions: {
        data: Array<{
            _id: string;
            name: string;
            display_name: Record<string, string>;
            resource: string;
            action: string;
        }>;
    };
}>();

const getTranslation = (translations: Record<string, string>) => {
    return translations?.[locale.value] || translations?.de || '';
};

const isSuperAdmin = computed(() => props.role.data.name === 'super_admin');

// Group permissions by resource
const permissionsByResource = computed(() => {
    const grouped: Record<string, Array<typeof props.allPermissions.data[0]>> = {};
    for (const perm of props.allPermissions.data) {
        if (!grouped[perm.resource]) {
            grouped[perm.resource] = [];
        }
        grouped[perm.resource].push(perm);
    }
    return grouped;
});

const resources = computed(() => Object.keys(permissionsByResource.value).sort());

const actions = computed(() => {
    const actionSet = new Set<string>();
    for (const perm of props.allPermissions.data) {
        actionSet.add(perm.action);
    }
    return Array.from(actionSet).sort();
});

// Track selected permission IDs
const selectedPermissionIds = ref<number[]>(
    props.role.data.permissions?.map(p => parseInt(p._id)) || []
);

const isChecked = (resource: string, action: string): boolean => {
    const perm = props.allPermissions.data.find(
        p => p.resource === resource && p.action === action
    );
    return perm ? selectedPermissionIds.value.includes(parseInt(perm._id)) : false;
};

const hasPermission = (resource: string, action: string): boolean => {
    return props.allPermissions.data.some(p => p.resource === resource && p.action === action);
};

const togglePermission = (resource: string, action: string) => {
    const perm = props.allPermissions.data.find(
        p => p.resource === resource && p.action === action
    );
    if (!perm) return;
    const id = parseInt(perm._id);
    const idx = selectedPermissionIds.value.indexOf(id);
    if (idx >= 0) {
        selectedPermissionIds.value.splice(idx, 1);
    } else {
        selectedPermissionIds.value.push(id);
    }
};

const saving = ref(false);

const savePermissions = () => {
    saving.value = true;
    router.put(`/admin/roles/${props.role.data._id}/permissions`, {
        permission_ids: selectedPermissionIds.value,
    }, {
        onFinish: () => { saving.value = false; },
    });
};

const goBack = () => {
    router.get('/admin/roles');
};
</script>

<template>
    <AdminLayout :title="getTranslation(role.data.display_name)">
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-2">
                <Button
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    text
                    rounded
                    @click="goBack"
                />
                <div>
                    <h1 class="text-2xl font-bold text-surface-900 dark:text-surface-0">
                        {{ getTranslation(role.data.display_name) }}
                    </h1>
                    <p class="text-surface-500 text-sm">{{ role.data.name }}</p>
                </div>
                <Tag
                    v-if="role.data.is_system"
                    :value="t('roles.systemRole')"
                    severity="info"
                    class="ml-2"
                />
            </div>
            <p class="text-surface-600 ml-12">{{ getTranslation(role.data.description) }}</p>
        </div>

        <!-- Super admin note -->
        <div v-if="isSuperAdmin" class="card mb-6 bg-red-50 border border-red-200">
            <div class="flex items-center gap-3">
                <i class="pi pi-shield text-red-500 text-2xl"></i>
                <div>
                    <h3 class="font-semibold text-red-700">{{ t('roles.superAdminNote') }}</h3>
                    <p class="text-red-600 text-sm">{{ t('roles.superAdminDesc') }}</p>
                </div>
            </div>
        </div>

        <!-- Permission Matrix -->
        <div v-else class="card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">{{ t('roles.permissionMatrix') }}</h2>
                <Button
                    :label="t('common.save')"
                    icon="pi pi-check"
                    :loading="saving"
                    @click="savePermissions"
                    :disabled="role.data.is_system"
                />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-sm">
                    <thead>
                        <tr class="bg-surface-100 dark:bg-surface-800">
                            <th class="text-left px-4 py-3 font-semibold text-surface-700 dark:text-surface-200 border-b">
                                {{ t('roles.resource') }}
                            </th>
                            <th
                                v-for="action in actions"
                                :key="action"
                                class="text-center px-4 py-3 font-semibold text-surface-700 dark:text-surface-200 border-b capitalize"
                            >
                                {{ action }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="resource in resources"
                            :key="resource"
                            class="border-b border-surface-200 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-800"
                        >
                            <td class="px-4 py-3 font-medium text-surface-800 dark:text-surface-200 capitalize">
                                {{ resource }}
                            </td>
                            <td
                                v-for="action in actions"
                                :key="action"
                                class="text-center px-4 py-3"
                            >
                                <Checkbox
                                    v-if="hasPermission(resource, action)"
                                    :modelValue="isChecked(resource, action)"
                                    :binary="true"
                                    :disabled="role.data.is_system"
                                    @update:modelValue="togglePermission(resource, action)"
                                />
                                <span v-else class="text-surface-300">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-surface-500">
                {{ selectedPermissionIds.length }} {{ t('roles.permissionsSelected') }}
            </div>
        </div>
    </AdminLayout>
</template>
