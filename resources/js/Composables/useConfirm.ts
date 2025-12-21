import { useConfirm as usePrimeConfirm } from 'primevue/useconfirm';

export function useConfirm() {
    const confirm = usePrimeConfirm();

    function confirmAction(options: {
        message: string;
        header?: string;
        icon?: string;
        acceptLabel?: string;
        rejectLabel?: string;
        onAccept: () => void;
        onReject?: () => void;
    }) {
        confirm.require({
            message: options.message,
            header: options.header ?? 'Confirm',
            icon: options.icon ?? 'pi pi-exclamation-triangle',
            acceptLabel: options.acceptLabel ?? 'Yes',
            rejectLabel: options.rejectLabel ?? 'No',
            accept: options.onAccept,
            reject: options.onReject,
        });
    }

    function confirmDelete(options: {
        message?: string;
        onAccept: () => void;
    }) {
        confirmAction({
            message: options.message ?? 'Are you sure you want to delete this item?',
            header: 'Delete Confirmation',
            icon: 'pi pi-trash',
            acceptLabel: 'Delete',
            rejectLabel: 'Cancel',
            onAccept: options.onAccept,
        });
    }

    return { confirmAction, confirmDelete };
}
