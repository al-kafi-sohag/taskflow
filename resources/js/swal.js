import Swal from 'sweetalert2';

// Base instance: styling fully controlled via CSS (buttonsStyling: false)
// so every dialog follows the app's design tokens instead of SweetAlert2 defaults.
const swalTheme = Swal.mixin({
    buttonsStyling: false,
    reverseButtons: true,
    focusConfirm: false,
    customClass: {
        popup: 'tf-swal-popup',
        title: 'tf-swal-title',
        htmlContainer: 'tf-swal-text',
        actions: 'tf-swal-actions',
        confirmButton: 'tf-swal-btn tf-swal-btn--primary',
        cancelButton: 'tf-swal-btn tf-swal-btn--cancel',
        denyButton: 'tf-swal-btn tf-swal-btn--danger',
    },
});

/**
 * Destructive-action confirmation (delete, remove, etc).
 * Confirm button renders in the danger token color.
 */
export function confirmDelete({
    title = 'Delete this item?',
    text = 'This action can be undone by an administrator.',
    confirmText = 'Yes, delete it',
    cancelText = 'Cancel',
} = {}) {
    return swalTheme.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        customClass: {
            popup: 'tf-swal-popup',
            title: 'tf-swal-title',
            htmlContainer: 'tf-swal-text',
            actions: 'tf-swal-actions',
            confirmButton: 'tf-swal-btn tf-swal-btn--danger',
            cancelButton: 'tf-swal-btn tf-swal-btn--cancel',
            icon: 'tf-swal-icon tf-swal-icon--warning',
        },
    });
}

/** Quiet, auto-dismissing success toast (bottom of a completed action). */
export function successAlert(text, title = 'Success') {
    return swalTheme.fire({
        title,
        text,
        icon: 'success',
        timer: 1600,
        showConfirmButton: false,
        customClass: {
            popup: 'tf-swal-popup tf-swal-popup--toast',
            title: 'tf-swal-title',
            htmlContainer: 'tf-swal-text',
            icon: 'tf-swal-icon tf-swal-icon--success',
        },
    });
}

/** Error alert with a single acknowledge button. */
export function errorAlert(text, title = 'Something went wrong') {
    return swalTheme.fire({
        title,
        text,
        icon: 'error',
        confirmButtonText: 'Got it',
        customClass: {
            popup: 'tf-swal-popup',
            title: 'tf-swal-title',
            htmlContainer: 'tf-swal-text',
            actions: 'tf-swal-actions',
            confirmButton: 'tf-swal-btn tf-swal-btn--primary',
            icon: 'tf-swal-icon tf-swal-icon--error',
        },
    });
}

export default swalTheme;
