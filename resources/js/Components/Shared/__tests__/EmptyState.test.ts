import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import EmptyState from '@/Components/Shared/EmptyState.vue';

describe('EmptyState', () => {
    it('renders title', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'No products found' },
        });

        expect(wrapper.find('[data-testid="empty-state"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('No products found');
    });

    it('renders description when provided', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'No data', description: 'Try adjusting your filters' },
        });

        expect(wrapper.text()).toContain('Try adjusting your filters');
    });

    it('does not render description when empty', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'No data' },
        });

        const paragraphs = wrapper.findAll('p');
        expect(paragraphs.length).toBe(0);
    });

    it('uses default icon pi-inbox', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'Empty' },
        });

        const icon = wrapper.find('i');
        expect(icon.classes()).toContain('pi');
        expect(icon.classes()).toContain('pi-inbox');
    });

    it('uses custom icon when provided', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'Empty', icon: 'pi pi-search' },
        });

        const icon = wrapper.find('i');
        expect(icon.classes()).toContain('pi-search');
    });

    it('renders action slot', () => {
        const wrapper = mount(EmptyState, {
            props: { title: 'No data' },
            slots: {
                action: '<button>Create</button>',
            },
        });

        expect(wrapper.text()).toContain('Create');
    });
});
