<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppSidebar from './Components/AppSidebar.vue';
import AppTopbar from './Components/AppTopbar.vue';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

defineProps<{
    title?: string;
}>();

const sidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
};

const toggleMobileSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value;
};

const closeMobileSidebar = () => {
    mobileSidebarOpen.value = false;
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Head :title="title" />
        <Toast position="top-right" />
        <ConfirmDialog />

        <!-- Mobile sidebar overlay -->
        <div
            v-if="mobileSidebarOpen"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
            @click="closeMobileSidebar"
        />

        <!-- Sidebar -->
        <AppSidebar
            :collapsed="sidebarCollapsed"
            :mobile-open="mobileSidebarOpen"
            @close-mobile="closeMobileSidebar"
        />

        <!-- Main content -->
        <div
            class="transition-all duration-300"
            :class="sidebarCollapsed ? 'lg:ml-20' : 'lg:ml-64'"
        >
            <!-- Topbar -->
            <AppTopbar
                @toggle-sidebar="toggleSidebar"
                @toggle-mobile-sidebar="toggleMobileSidebar"
                @logout="logout"
            />

            <!-- Page content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
