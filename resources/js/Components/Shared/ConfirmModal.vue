<script setup lang="ts">
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

interface Props {
    visible: boolean;
    header?: string;
    message: string;
    icon?: string;
    acceptLabel?: string;
    rejectLabel?: string;
    severity?: 'danger' | 'warn' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
    header: 'Confirm',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Yes',
    rejectLabel: 'Cancel',
    severity: 'warn',
});

const emit = defineEmits<{
    'update:visible': [value: boolean];
    accept: [];
    reject: [];
}>();

const severityClass: Record<string, string> = {
    danger: 'text-red-500',
    warn: 'text-amber-500',
    info: 'text-blue-500',
};

function onAccept() {
    emit('accept');
    emit('update:visible', false);
}

function onReject() {
    emit('reject');
    emit('update:visible', false);
}
</script>

<template>
    <Dialog
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        :header="header"
        modal
        :style="{ width: '28rem' }"
        data-testid="confirm-modal"
    >
        <div class="flex items-start gap-4">
            <i :class="[icon, 'text-3xl', severityClass[severity]]" />
            <p class="text-gray-700">{{ message }}</p>
        </div>
        <template #footer>
            <Button
                :label="rejectLabel"
                severity="secondary"
                text
                @click="onReject"
                data-testid="confirm-reject"
            />
            <Button
                :label="acceptLabel"
                :severity="severity === 'danger' ? 'danger' : undefined"
                @click="onAccept"
                data-testid="confirm-accept"
            />
        </template>
    </Dialog>
</template>
