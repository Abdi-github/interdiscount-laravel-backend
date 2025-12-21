<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="mb-6 flex items-center justify-between" data-testid="page-header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900" data-testid="page-title">{{ title }}</h1>
            <p v-if="description" class="mt-1 text-sm text-gray-500" data-testid="page-description">{{ description }}</p>
        </div>
        <div class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>
