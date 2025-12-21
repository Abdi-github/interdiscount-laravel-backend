<script setup lang="ts">
import { computed } from 'vue';
import Tag from 'primevue/tag';

interface Props {
    status: string;
    type?: 'order' | 'payment' | 'product' | 'transfer' | 'account';
}

const props = withDefaults(defineProps<Props>(), {
    type: 'order',
});

const severityMap: Record<string, Record<string, string>> = {
    order: {
        placed: 'info',
        confirmed: 'info',
        processing: 'warn',
        shipped: 'warn',
        delivered: 'success',
        ready_for_pickup: 'warn',
        picked_up: 'success',
        cancelled: 'danger',
        returned: 'danger',
        pickup_expired: 'secondary',
    },
    payment: {
        pending: 'warn',
        processing: 'warn',
        succeeded: 'success',
        failed: 'danger',
        refunded: 'secondary',
        partially_refunded: 'warn',
    },
    product: {
        active: 'success',
        inactive: 'secondary',
        draft: 'warn',
        archived: 'secondary',
        out_of_stock: 'danger',
    },
    transfer: {
        requested: 'info',
        approved: 'info',
        shipped: 'warn',
        received: 'success',
        cancelled: 'danger',
        rejected: 'danger',
    },
    account: {
        active: 'success',
        inactive: 'danger',
        verified: 'success',
        unverified: 'warn',
    },
};

const severity = computed(() => {
    const s = props.status?.toLowerCase();
    return (severityMap[props.type]?.[s] ?? 'secondary') as
        'success' | 'info' | 'warn' | 'danger' | 'secondary' | 'contrast' | undefined;
});

const label = computed(() => {
    return props.status.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
});
</script>

<template>
    <Tag :value="label" :severity="severity" data-testid="status-badge" />
</template>
