import { maskRupiah } from './rupiah';

// Terapkan format rupiah ke semua input dengan class .rupiah-mask
// Tampilan: terformat rupiah
// Nilai yang dikirim ke backend: digit murni (tanpa Rp/titik), diset sebelum submit form
export function initRupiahMasks() {
    const inputs = document.querySelectorAll('input.rupiah-mask');

    inputs.forEach((input) => {
        // Inisialisasi jika sudah ada nilai awal (misal dari edit form)
        if (input.value) {
            const digits = input.value.replace(/\D/g, '');
            input.dataset.raw = digits;
            input.value = digits;
            maskRupiah(input);
        }

        input.addEventListener('input', () => {
            // Hanya izinkan digit terlebih dahulu
            let digits = input.value.replace(/\D/g, '');

            // Simpan versi murni ke data attribute
            input.dataset.raw = digits;

            // Tampilkan terformat rupiah
            input.value = digits;
            maskRupiah(input);
        });
    });

    // Pastikan sebelum submit, nilai yang terkirim ke backend adalah digit murni
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        const rupiahInputs = form.querySelectorAll('input.rupiah-mask');
        rupiahInputs.forEach((input) => {
            const el = /** @type {HTMLInputElement} */ (input);
            const raw = el.dataset.raw ? el.dataset.raw.replace(/\D/g, '') : el.value.replace(/\D/g, '');
            el.value = raw;
        });
    });
}

// Batasi panjang input berdasarkan data-maxlength
export function initLengthLimit() {
    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('limit-length')) return;

        const max = parseInt(el.dataset.maxlength || el.getAttribute('maxlength') || '0', 10);
        if (!max) return;

        if (el.value.length > max) {
            el.value = el.value.slice(0, max);
        }
    });
}

// Hanya angka untuk input dengan class .numeric-only (yang BUKAN rupiah-mask)
export function initNumericOnly() {
    document.addEventListener('input', (e) => {
        const el = e.target;
        if (!el.classList || !el.classList.contains('numeric-only')) return;

        // Jika juga punya rupiah-mask, sudah ditangani di initRupiahMasks
        if (el.classList.contains('rupiah-mask')) return;

        let digits = el.value.replace(/\D/g, '');
        const maxDigits = el.dataset.maxdigits ? parseInt(el.dataset.maxdigits, 10) : null;
        if (maxDigits && digits.length > maxDigits) {
            digits = digits.slice(0, maxDigits);
        }
        el.value = digits;
    });
}

export function initGlobalInputMasks() {
    if (typeof window === 'undefined') return;
    initRupiahMasks();
    initLengthLimit();
    initNumericOnly();
}
