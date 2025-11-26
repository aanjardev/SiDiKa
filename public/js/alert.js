/**
 * Alert Helper System untuk SiDiKa Admin
 * Menggunakan SweetAlert2 untuk konfirmasi dan notifikasi
 */

// Fungsi helper untuk konfirmasi delete
function confirmDelete(message = 'Data ini akan dihapus secara permanen!', title = 'Yakin ingin menghapus?') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    });
}

// Fungsi helper untuk konfirmasi umum
function confirmAction(message, title = 'Konfirmasi', confirmText = 'Ya', cancelText = 'Batal') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText
    });
}

// Fungsi helper untuk alert success
function showSuccess(message, title = 'Berhasil!') {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// Fungsi helper untuk alert error
function showError(message, title = 'Terjadi Kesalahan') {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        confirmButtonText: 'OK'
    });
}

// Fungsi helper untuk alert warning
function showWarning(message, title = 'Perhatian') {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: message,
        confirmButtonText: 'OK'
    });
}

// Fungsi helper untuk alert info
function showInfo(message, title = 'Informasi') {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}

// Auto-handle delete buttons dengan class .btn-delete
document.addEventListener('DOMContentLoaded', () => {
    // Handle delete buttons dengan class .btn-delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('form');
            if (!form) return;

            const message = this.dataset.message || 'Data ini akan dihapus secara permanen!';
            const title = this.dataset.title || 'Yakin ingin menghapus?';

            confirmDelete(message, title).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Handle form dengan onsubmit confirm (legacy support)
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        const originalOnsubmit = form.onsubmit;
        form.onsubmit = null;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Extract message from onsubmit attribute if exists
            const onsubmitAttr = form.getAttribute('onsubmit');
            let message = 'Data ini akan dihapus secara permanen!';
            let title = 'Yakin ingin menghapus?';
            
            if (onsubmitAttr) {
                const match = onsubmitAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
                if (match) {
                    message = match[1];
                }
            }
            
            confirmDelete(message, title).then((result) => {
                if (result.isConfirmed) {
                    form.removeAttribute('onsubmit');
                    form.submit();
                }
            });
        });
    });

    // Handle flash success dari session
    const flashSuccess = document.querySelector('meta[name="flash-success"]');
    if (flashSuccess && flashSuccess.content) {
        showSuccess(flashSuccess.content);
    }
});

// Export functions untuk penggunaan global
window.confirmDelete = confirmDelete;
window.confirmAction = confirmAction;
window.showSuccess = showSuccess;
window.showError = showError;
window.showWarning = showWarning;
window.showInfo = showInfo;
