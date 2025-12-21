import { describe, it, expect, vi, beforeEach } from 'vitest';

const mockRequire = vi.fn();

vi.mock('primevue/useconfirm', () => ({
    useConfirm: () => ({ require: mockRequire }),
}));

import { useConfirm } from '@/Composables/useConfirm';

describe('useConfirm', () => {
    beforeEach(() => {
        mockRequire.mockClear();
    });

    describe('confirmAction', () => {
        it('calls confirm.require with provided options', () => {
            const { confirmAction } = useConfirm();
            const onAccept = vi.fn();

            confirmAction({
                message: 'Are you sure?',
                header: 'Confirm Action',
                icon: 'pi pi-check',
                acceptLabel: 'Yes, do it',
                rejectLabel: 'Cancel',
                onAccept,
            });

            expect(mockRequire).toHaveBeenCalledWith({
                message: 'Are you sure?',
                header: 'Confirm Action',
                icon: 'pi pi-check',
                acceptLabel: 'Yes, do it',
                rejectLabel: 'Cancel',
                accept: onAccept,
                reject: undefined,
            });
        });

        it('uses default values for optional fields', () => {
            const { confirmAction } = useConfirm();
            const onAccept = vi.fn();

            confirmAction({
                message: 'Continue?',
                onAccept,
            });

            expect(mockRequire).toHaveBeenCalledWith({
                message: 'Continue?',
                header: 'Confirm',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Yes',
                rejectLabel: 'No',
                accept: onAccept,
                reject: undefined,
            });
        });

        it('passes onReject callback', () => {
            const { confirmAction } = useConfirm();
            const onAccept = vi.fn();
            const onReject = vi.fn();

            confirmAction({
                message: 'Test',
                onAccept,
                onReject,
            });

            expect(mockRequire).toHaveBeenCalledWith(
                expect.objectContaining({
                    reject: onReject,
                }),
            );
        });
    });

    describe('confirmDelete', () => {
        it('calls confirmAction with delete-specific defaults', () => {
            const { confirmDelete } = useConfirm();
            const onAccept = vi.fn();

            confirmDelete({ onAccept });

            expect(mockRequire).toHaveBeenCalledWith({
                message: 'Are you sure you want to delete this item?',
                header: 'Delete Confirmation',
                icon: 'pi pi-trash',
                acceptLabel: 'Delete',
                rejectLabel: 'Cancel',
                accept: onAccept,
                reject: undefined,
            });
        });

        it('accepts custom delete message', () => {
            const { confirmDelete } = useConfirm();
            const onAccept = vi.fn();

            confirmDelete({
                message: 'Delete this product?',
                onAccept,
            });

            expect(mockRequire).toHaveBeenCalledWith(
                expect.objectContaining({
                    message: 'Delete this product?',
                }),
            );
        });
    });
});
