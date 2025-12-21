<script setup lang="ts">
import { computed } from 'vue';
import Button from 'primevue/button';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    status: number;
}>();

const config = computed(() => {
    const map: Record<number, { title: string; description: string; icon: string }> = {
        403: {
            title: '403 — Zugriff verweigert',
            description: 'Sie haben keine Berechtigung, auf diese Seite zuzugreifen.',
            icon: 'pi pi-lock',
        },
        404: {
            title: '404 — Seite nicht gefunden',
            description: 'Die gesuchte Seite existiert nicht oder wurde verschoben.',
            icon: 'pi pi-search',
        },
        500: {
            title: '500 — Serverfehler',
            description: 'Ein interner Serverfehler ist aufgetreten. Bitte versuchen Sie es später erneut.',
            icon: 'pi pi-exclamation-triangle',
        },
        503: {
            title: '503 — Wartungsmodus',
            description: 'Die Anwendung wird derzeit gewartet. Bitte versuchen Sie es später erneut.',
            icon: 'pi pi-wrench',
        },
    };

    return map[props.status] ?? {
        title: `${props.status} — Fehler`,
        description: 'Ein unerwarteter Fehler ist aufgetreten.',
        icon: 'pi pi-exclamation-circle',
    };
});

function goHome() {
    router.visit('/admin/dashboard');
}

function goBack() {
    window.history.back();
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-surface-50 px-4">
        <div class="text-center max-w-lg">
            <div class="mb-6">
                <i :class="config.icon" class="text-6xl text-surface-400"></i>
            </div>
            <h1 class="text-3xl font-bold text-surface-800 mb-3">{{ config.title }}</h1>
            <p class="text-surface-500 text-lg mb-8">{{ config.description }}</p>
            <div class="flex gap-3 justify-center">
                <Button
                    label="Zurück"
                    icon="pi pi-arrow-left"
                    severity="secondary"
                    outlined
                    @click="goBack"
                />
                <Button
                    label="Dashboard"
                    icon="pi pi-home"
                    @click="goHome"
                />
            </div>
        </div>
    </div>
</template>
