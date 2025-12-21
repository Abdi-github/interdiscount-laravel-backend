import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import PageHeader from '@/Components/Shared/PageHeader.vue';

describe('PageHeader', () => {
    it('renders title', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Products' },
        });

        expect(wrapper.find('[data-testid="page-title"]').text()).toBe('Products');
    });

    it('renders description when provided', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Products', description: 'Manage your products' },
        });

        expect(wrapper.find('[data-testid="page-description"]').text()).toBe('Manage your products');
    });

    it('does not render description when not provided', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Products' },
        });

        expect(wrapper.find('[data-testid="page-description"]').exists()).toBe(false);
    });

    it('renders action slot content', () => {
        const wrapper = mount(PageHeader, {
            props: { title: 'Products' },
            slots: {
                actions: '<button>Add Product</button>',
            },
        });

        expect(wrapper.text()).toContain('Add Product');
    });
});
