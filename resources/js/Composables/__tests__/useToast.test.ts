import { describe, it, expect, vi } from 'vitest';

const mockAdd = vi.fn();

vi.mock('primevue/usetoast', () => ({
    useToast: () => ({ add: mockAdd }),
}));

import { useToast } from '@/Composables/useToast';

describe('useToast', () => {
    beforeEach(() => {
        mockAdd.mockClear();
    });

    it('success() calls toast.add with severity success', () => {
        const { success } = useToast();
        success('Item created');
        expect(mockAdd).toHaveBeenCalledWith({
            severity: 'success',
            summary: 'Success',
            detail: 'Item created',
            life: 3000,
        });
    });

    it('success() accepts custom title', () => {
        const { success } = useToast();
        success('Done', 'Custom Title');
        expect(mockAdd).toHaveBeenCalledWith({
            severity: 'success',
            summary: 'Custom Title',
            detail: 'Done',
            life: 3000,
        });
    });

    it('error() calls toast.add with severity error and longer life', () => {
        const { error } = useToast();
        error('Something failed');
        expect(mockAdd).toHaveBeenCalledWith({
            severity: 'error',
            summary: 'Error',
            detail: 'Something failed',
            life: 5000,
        });
    });

    it('info() calls toast.add with severity info', () => {
        const { info } = useToast();
        info('For your information');
        expect(mockAdd).toHaveBeenCalledWith({
            severity: 'info',
            summary: 'Info',
            detail: 'For your information',
            life: 3000,
        });
    });

    it('warn() calls toast.add with severity warn', () => {
        const { warn } = useToast();
        warn('Be careful');
        expect(mockAdd).toHaveBeenCalledWith({
            severity: 'warn',
            summary: 'Warning',
            detail: 'Be careful',
            life: 4000,
        });
    });
});
