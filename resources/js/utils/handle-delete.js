(function () {
    'use strict';

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-action-delete');
        if (!btn) return;

        const message = btn.getAttribute('data-message')
            || 'Apakah Anda yakin ingin menghapus data ini?';

        // Jika tombol tidak berada dalam form → error
        if (!btn.form) {
            console.error("Error: Tombol delete tidak berada di dalam form.");
            return;
        }

        // Jika SweetAlert tersedia
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(message, 'Konfirmasi Hapus')
                .then(result => {
                    if (result.isConfirmed) {
                        btn.form.submit();
                    }
                });

            return;
        }

        // Fallback confirm biasa
        if (confirm(message)) {
            btn.form.submit();
        }
    });
})();
