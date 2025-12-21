import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import Error from '@/Pages/Error.vue';
import PrimeVue from 'primevue/config';

vi.mock('@inertiajs/vue3', () => ({
    router: { visit: vi.fn() },
}));

const mountError = (status: number) => {
    return mount(Error, {
        props: { status },
        global: {
            plugins: [[PrimeVue, { unstyled: true }]],
        },
    });
};

describe('Error Page', () => {
    it('renders 404 page with correct title and description', () => {
        const wrapper = mountError(404);

        expect(wrapper.find('h1').text()).toBe('404 — Seite nicht gefunden');
        expect(wrapper.find('p').text()).toContain('existiert nicht');
    });

    it('renders 403 page', () => {
        const wrapper = mountError(403);

        expect(wrapper.find('h1').text()).toBe('403 — Zugriff verweigert');
        expect(wrapper.find('p').text()).toContain('keine Berechtigung');
    });

    it('renders 500 page', () => {
        const wrapper = mountError(500);

        expect(wrapper.find('h1').text()).toBe('500 — Serverfehler');
        expect(wrapper.find('p').text()).toContain('interner Serverfehler');
    });

    it('renders 503 page', () => {
        const wrapper = mountError(503);

        expect(wrapper.find('h1').text()).toBe('503 — Wartungsmodus');
        expect(wrapper.find('p').text()).toContain('gewartet');
    });

    it('renders fallback for unknown status codes', () => {
        const wrapper = mountError(418);

        expect(wrapper.find('h1').text()).toBe('418 — Fehler');
        expect(wrapper.find('p').text()).toContain('unerwarteter Fehler');
    });

    it('renders back and dashboard buttons', () => {
        const wrapper = mountError(404);
        const buttons = wrapper.findAll('button');

        expect(buttons.length).toBe(2);
        expect(wrapper.text()).toContain('Zurück');
        expect(wrapper.text()).toContain('Dashboard');
    });

    it('calls router.visit on dashboard click', async () => {
        const { router } = await import('@inertiajs/vue3');
        const wrapper = mountError(404);

        const dashboardBtn = wrapper.findAll('button').find(b => b.text().includes('Dashboard'));
        await dashboardBtn!.trigger('click');

        expect(router.visit).toHaveBeenCalledWith('/admin/dashboard');
    });

    it('calls window.history.back on back click', async () => {
        const historyBack = vi.spyOn(window.history, 'back').mockImplementation(() => {});
        const wrapper = mountError(404);

        const backBtn = wrapper.findAll('button').find(b => b.text().includes('Zurück'));
        await backBtn!.trigger('click');

        expect(historyBack).toHaveBeenCalled();
        historyBack.mockRestore();
    });

    it('shows lock icon for 403', () => {
        const wrapper = mountError(403);
        expect(wrapper.find('i').classes()).toContain('pi-lock');
    });

    it('shows search icon for 404', () => {
        const wrapper = mountError(404);
        expect(wrapper.find('i').classes()).toContain('pi-search');
    });

    it('shows warning icon for 500', () => {
        const wrapper = mountError(500);
        expect(wrapper.find('i').classes()).toContain('pi-exclamation-triangle');
    });
});
