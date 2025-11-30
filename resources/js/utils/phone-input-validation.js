/**
 * UNIVERSAL PHONE INPUT HANDLER
 * Menggabungkan:
 * 1. Sanitizer numerik (input biasa)
 * 2. Display + Hidden Formatter (Cabang & Karyawan)
 */

(function () {
    'use strict';

    /* -------------------------------
     * Helper: Format 4-4-sisa
     * ------------------------------- */
    function formatPhone(digits) {
        if (!digits) return '';
        const p1 = digits.slice(0, 4);
        const p2 = digits.slice(4, 8);
        const rest = digits.slice(8);

        let out = p1;
        if (p2) out += '-' + p2;
        if (rest) out += '-' + rest;
        return out;
    }

    /* -------------------------------
     * Helper: Bersihkan angka & limit
     * ------------------------------- */
    function cleanDigits(value, max = 15) {
        let digits = (value || '').replace(/\D/g, '');
        return digits.slice(0, max);
    }

    /* ---------------------------------------------------------
     * MODE 1 — INPUT BIASA (General Phone Sanitization)
     * --------------------------------------------------------- */
    function initBasicSanitizer() {
        const selectors =
            'input[data-phone-validation], input[name*="telepon"], input[name*="telp"], input[name="no_telp"], input[name="identitas"]';

        document.querySelectorAll(selectors).forEach(input => {
            input.addEventListener('input', function () {
                this.value = cleanDigits(this.value, 15);
            });

            input.addEventListener('paste', function (e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                this.value = cleanDigits(paste, 15);
            });

            input.addEventListener('keypress', function (e) {
                const char = String.fromCharCode(e.which);
                if (!/[0-9]/.test(char) && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                }
            });
        });
    }

    /* ---------------------------------------------------------
     * MODE 2 — DISPLAY + HIDDEN (Cabang & Karyawan)
     * --------------------------------------------------------- */
    function initFormattedPhonePair(displayId, hiddenId) {
        const displayInput = document.getElementById(displayId);
        const hiddenInput = document.getElementById(hiddenId);

        if (!displayInput || !hiddenInput) return;

        // Initialize
        (function () {
            const raw = cleanDigits(hiddenInput.value, 15);
            hiddenInput.value = raw;
            displayInput.value = formatPhone(raw);
        })();

        // Input realtime
        displayInput.addEventListener('input', function () {
            const digits = cleanDigits(this.value, 15);
            hiddenInput.value = digits;
            displayInput.value = formatPhone(digits);
        });

        // Validasi saat blur
        displayInput.addEventListener('blur', function () {
            const digits = hiddenInput.value;
            const formControl = displayInput;
            const feedback = formControl.closest('.input-group').nextElementSibling;

            formControl.classList.remove('is-invalid');

            if (!digits) {
                formControl.classList.add('is-invalid');
                if (feedback?.classList.contains('invalid-feedback')) {
                    feedback.textContent = 'Nomor telepon wajib diisi.';
                }
                return;
            }

            const regex = /^(?:0|62|\+62)[0-9]{8,15}$/;
            if (!regex.test(digits)) {
                formControl.classList.add('is-invalid');
                if (feedback?.classList.contains('invalid-feedback')) {
                    feedback.textContent =
                        'Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.';
                }
            }
        });
    }

    /* -------------------------------
     * Auto Initialize Universal System
     * ------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {

        // Mode 1 (sanitizer global)
        initBasicSanitizer();

        // Mode 2 (formatted display + hidden)
        // Untuk Karyawan
        initFormattedPhonePair('nomor_telepon_display', 'nomor_telepon');

        // Untuk Cabang
        initFormattedPhonePair('branch_nomor_telepon_display', 'branch_nomor_telepon');
    });
})();
