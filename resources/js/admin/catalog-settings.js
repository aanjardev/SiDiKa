document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('settingsForm');
    if (!form) return;

    // Fokus awal pada nama website
    const siteNameInput = form.querySelector('input[name="site_name"]');
    if (siteNameInput) {
        siteNameInput.focus();
        const length = siteNameInput.value.length;
        siteNameInput.setSelectionRange(length, length);
    }

    // Tampilkan tombol simpan per card saat ada perubahan
    form.querySelectorAll('.card').forEach((card) => {
        const saveBtn = card.querySelector('.card-save-btn');
        const logoBtn = card.querySelector('.card-save-btn-logo');
        const targetBtn = logoBtn || saveBtn;
        if (!targetBtn) return;

        const markDirty = () => targetBtn.classList.add('btn-primary', 'show-save');
        card.addEventListener('input', markDirty);
        card.addEventListener('change', markDirty);
    });

    // Logic penghapusan banner / partner
    const partnerRouteTemplate = form.dataset.routePartnerDestroy;
    const bannerRouteTemplate = form.dataset.routeBannerDestroy;

    const pushUnique = (arr, value) => {
        if (!arr.includes(value)) arr.push(value);
    };

    let deletedPartnersArr = [];
    let deletedBannersArr = [];

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const row = btn.closest('tr');
        const id = row?.getAttribute('data-id');
        const type = row?.getAttribute('data-type');
        if (!row || !id || !type) return;

        const confirmFn = typeof window.confirmDelete === 'function'
            ? window.confirmDelete
            : (msg, title) => Promise.resolve({ isConfirmed: window.confirm(msg || title || 'Hapus data ini?') });

        confirmFn('Apakah Anda yakin ingin menghapus item ini?', 'Konfirmasi Hapus')
            .then((result) => {
                if (!result?.isConfirmed) return;

                const urlMap = {
                    partner: partnerRouteTemplate,
                    banner: bannerRouteTemplate,
                };
                const endpointTemplate = urlMap[type];
                if (!endpointTemplate) return;
                const finalUrl = endpointTemplate.replace(':id', id);

                btn.disabled = true;

                if (type === 'partner') {
                    pushUnique(deletedPartnersArr, id);
                    const field = document.getElementById('deletedPartners');
                    if (field) field.value = JSON.stringify(deletedPartnersArr);
                } else if (type === 'banner') {
                    pushUnique(deletedBannersArr, id);
                    const field = document.getElementById('deletedBanners');
                    if (field) field.value = JSON.stringify(deletedBannersArr);
                }

                row.style.transition = 'all 0.3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);

                if (form) {
                    form.requestSubmit();
                }

                // Optional: perform fetch delete
                if (finalUrl) {
                    fetch(finalUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                    }).catch(() => {});
                }
            });
    });

    // Validasi ukuran file client-side
    const fileInputs = Array.from(form.querySelectorAll('input[type="file"][data-max-bytes]'));

    const setInvalid = (input, message) => {
        input.classList.add('is-invalid');
        input.setCustomValidity(message || 'File tidak valid.');
        const feedback = input.nextElementSibling;
        if (feedback?.classList?.contains('invalid-feedback') && message) {
            feedback.textContent = message;
        }
    };

    const clearInvalid = (input) => {
        input.classList.remove('is-invalid');
        input.setCustomValidity('');
    };

    const validateFile = (input) => {
        const maxBytes = parseInt(input.dataset.maxBytes || '0', 10);
        const maxLabel = input.dataset.maxLabel || null;
        const file = input.files?.[0] || null;

        clearInvalid(input);

        if (!file || !maxBytes) return true;

        if (file.size > maxBytes) {
            const msg = `Ukuran file terlalu besar. Maksimal ${maxLabel || Math.round(maxBytes / 1024 / 1024) + 'MB'}.`;
            setInvalid(input, msg);
            return false;
        }

        if (file.type && !file.type.startsWith('image/')) {
            setInvalid(input, 'Format file harus berupa gambar.');
            return false;
        }

        return true;
    };

    fileInputs.forEach((input) => {
        input.addEventListener('change', () => validateFile(input));
    });

    form.addEventListener('submit', (e) => {
        let firstInvalid = null;
        fileInputs.forEach((input) => {
            const ok = validateFile(input);
            if (!ok && !firstInvalid) firstInvalid = input;
        });

        if (firstInvalid) {
            e.preventDefault();
            e.stopPropagation();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    });
});
