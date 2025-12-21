<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, watch } from 'vue';
import Button from 'primevue/button';
import Menu from 'primevue/menu';
import { ref } from 'vue';

const emit = defineEmits<{
    toggleSidebar: [];
    toggleMobileSidebar: [];
    logout: [];
}>();

const { t, locale } = useI18n();
const page = usePage();

const user = computed(() => (page.props as any).auth?.user);

const userMenuRef = ref();
const localeMenuRef = ref();

const userMenuItems = computed(() => [
    {
        label: t('common.profile'),
        icon: 'pi pi-user',
        command: () => {
            window.location.href = route('profile.show');
        },
    },
    { separator: true },
    {
        label: t('common.logout'),
        icon: 'pi pi-sign-out',
        command: () => emit('logout'),
    },
]);

const locales = [
    { code: 'de', label: 'Deutsch', flag: 'DE' },
    { code: 'en', label: 'English', flag: 'EN' },
    { code: 'fr', label: 'Français', flag: 'FR' },
    { code: 'it', label: 'Italiano', flag: 'IT' },
];

const currentLocale = computed(() => locales.find((l) => l.code === locale.value) ?? locales[0]);

const localeMenuItems = computed(() =>
    locales.map((l) => ({
        label: l.label,
        command: () => {
            locale.value = l.code;
        },
        class: l.code === locale.value ? 'font-bold' : '',
    }))
);

const toggleUserMenu = (event: Event) => {
    userMenuRef.value.toggle(event);
};

const toggleLocaleMenu = (event: Event) => {
    localeMenuRef.value.toggle(event);
};

// Flash messages
const flash = computed(() => (page.props as any).flash ?? {});
</script>

<template>
    <header
        class="sticky top-0 z-30 bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6"
        data-testid="topbar"
    >
        <!-- Left: hamburger + breadcrumb -->
        <div class="flex items-center gap-3">
            <!-- Desktop toggle -->
            <button
                class="hidden lg:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100"
                @click="$emit('toggleSidebar')"
                data-testid="toggle-sidebar"
            >
                <i class="pi pi-bars text-lg"></i>
            </button>
            <!-- Mobile toggle -->
            <button
                class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:bg-gray-100"
                @click="$emit('toggleMobileSidebar')"
                data-testid="toggle-mobile-sidebar"
            >
                <i class="pi pi-bars text-lg"></i>
            </button>
        </div>

        <!-- Right: locale + user -->
        <div class="flex items-center gap-2">
            <!-- Locale switcher -->
            <Button
                type="button"
                :label="currentLocale.flag"
                severity="secondary"
                text
                size="small"
                @click="toggleLocaleMenu"
                data-testid="locale-switcher"
            />
            <Menu ref="localeMenuRef" :model="localeMenuItems" :popup="true" />

            <!-- User menu -->
            <Button
                type="button"
                :label="user?.name ?? ''"
                severity="secondary"
                text
                size="small"
                icon="pi pi-user"
                @click="toggleUserMenu"
                data-testid="user-menu-button"
            />
            <Menu ref="userMenuRef" :model="userMenuItems" :popup="true" />
        </div>
    </header>
</template>
