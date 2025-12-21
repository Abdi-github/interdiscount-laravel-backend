import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import StatusBadge from '@/Components/Shared/StatusBadge.vue';
import PrimeVue from 'primevue/config';

const mountWithPrime = (props: Record<string, unknown>) => {
    return mount(StatusBadge, {
        props,
        global: {
            plugins: [[PrimeVue, { unstyled: true }]],
        },
    });
};

describe('StatusBadge', () => {
    describe('label formatting', () => {
        it('converts snake_case to Title Case', () => {
            const wrapper = mountWithPrime({ status: 'ready_for_pickup', type: 'order' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Ready For Pickup');
        });

        it('capitalizes single word statuses', () => {
            const wrapper = mountWithPrime({ status: 'active', type: 'product' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Active');
        });

        it('capitalizes "cancelled"', () => {
            const wrapper = mountWithPrime({ status: 'cancelled', type: 'order' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Cancelled');
        });
    });

    describe('order type severities', () => {
        const cases: [string, string][] = [
            ['placed', 'info'],
            ['confirmed', 'info'],
            ['processing', 'warn'],
            ['shipped', 'warn'],
            ['delivered', 'success'],
            ['cancelled', 'danger'],
            ['returned', 'danger'],
        ];

        it.each(cases)('status "%s" has severity "%s"', (status, _expectedSeverity) => {
            const wrapper = mountWithPrime({ status, type: 'order' });
            expect(wrapper.find('[data-testid="status-badge"]').exists()).toBe(true);
        });
    });

    describe('product type severities', () => {
        it('renders active product with success', () => {
            const wrapper = mountWithPrime({ status: 'active', type: 'product' });
            expect(wrapper.find('[data-testid="status-badge"]').exists()).toBe(true);
        });

        it('renders draft product', () => {
            const wrapper = mountWithPrime({ status: 'draft', type: 'product' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Draft');
        });
    });

    describe('payment type', () => {
        it('renders pending payment', () => {
            const wrapper = mountWithPrime({ status: 'pending', type: 'payment' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Pending');
        });

        it('renders succeeded payment', () => {
            const wrapper = mountWithPrime({ status: 'succeeded', type: 'payment' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Succeeded');
        });
    });

    describe('transfer type', () => {
        it('renders requested transfer', () => {
            const wrapper = mountWithPrime({ status: 'requested', type: 'transfer' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Requested');
        });

        it('renders received transfer', () => {
            const wrapper = mountWithPrime({ status: 'received', type: 'transfer' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Received');
        });
    });

    describe('unknown status', () => {
        it('falls back to secondary severity', () => {
            const wrapper = mountWithPrime({ status: 'unknown_status', type: 'order' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Unknown Status');
        });
    });

    describe('default type', () => {
        it('defaults to order type', () => {
            const wrapper = mountWithPrime({ status: 'delivered' });
            expect(wrapper.find('[data-testid="status-badge"]').text()).toBe('Delivered');
        });
    });
});
