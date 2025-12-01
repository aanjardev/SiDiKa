document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("qcForm");
    // Try to find the page's "Kembali" button robustly: prefer quality-control href,
    // otherwise find any visible link/button whose text contains 'kembali'.
    let btnKembali = document.querySelector('a[href*="quality-control"]');
    if (!btnKembali) {
        const candidates = Array.from(document.querySelectorAll("a, button"));
        btnKembali =
            candidates.find((el) => {
                try {
                    const txt = (el.textContent || "").trim().toLowerCase();
                    // also ensure element is visible
                    const style = window.getComputedStyle(el);
                    const visible =
                        style &&
                        style.display !== "none" &&
                        style.visibility !== "hidden" &&
                        el.offsetParent !== null;
                    return visible && txt.includes("kembali");
                } catch (e) {
                    return false;
                }
            }) || null;
    }
    const smartActionBtn = document.getElementById("smartActionBtn");
    const smartActionText = document.getElementById("smartActionText");
    const smartActionIcon = document.getElementById("smartActionIcon");
    const smartActionHint = document.getElementById("smartActionHint");
    const statusQcSelect = form?.querySelector('[name="status_qc"]');
    let isFormDirty = false;

    // ===== SMART ACTION BUTTON LOGIC =====
    const statusToAction = {
        menunggu_qc: {
            action: "draft",
            text: "Simpan Draft",
            iconClass: "fa-pen-to-square",
            btnClass: "btn-light border text-secondary",
            hint: "Menyimpan perubahan sebagai draft (status: Menunggu QC)",
        },
        lolos_qc: {
            action: "save",
            text: "Simpan & Loloskan",
            iconClass: "fa-check-circle",
            btnClass: "btn-success",
            hint: "Menyimpan dan menandai produk lolos QC",
        },
        gagal_qc: {
            action: "archive",
            text: "Arsipkan Produk",
            iconClass: "fa-archive",
            btnClass: "btn-danger",
            hint: "Menyimpan dan mengarsipkan produk (Gagal QC)",
        },
        // 'diarsipkan': { action: 'archive', text: 'Tetap di Arsip', iconClass: 'fa-archive', btnClass: 'btn-secondary', hint: 'Tetap menyimpan dalam status arsip' }
    };

    function updateSmartButton() {
        if (!statusQcSelect) return;
        const status = statusQcSelect.value;
        const config = statusToAction[status] || statusToAction["menunggu_qc"];

        // Update button icon, text dan class
        smartActionText.textContent = config.text;
        smartActionIcon.className = `fa-solid ${config.iconClass} me-2`;
        smartActionBtn.className = `btn fw-medium py-2 ${config.btnClass}`;
        smartActionBtn.setAttribute("data-action", config.action);
        smartActionHint.textContent = config.hint;

        // subtle highlight on the select (no giant font)
        if (statusQcSelect) {
            statusQcSelect.classList.toggle(
                "status-highlight",
                status !== "menunggu_qc"
            );
        }
    }

    // Update saat status berubah
    if (statusQcSelect) {
        statusQcSelect.addEventListener("change", updateSmartButton);
        // Initial update
        updateSmartButton();
    }

    // Pass action ke form handler via data attribute
    if (smartActionBtn) {
        smartActionBtn.addEventListener("click", function (e) {
            const action = this.getAttribute("data-action") || "save";
            // Create hidden input untuk membawa action ke backend
            let actionInput = form.querySelector('input[name="action"]');
            if (!actionInput) {
                actionInput = document.createElement("input");
                actionInput.type = "hidden";
                actionInput.name = "action";
                form.appendChild(actionInput);
            }
            actionInput.value = action;
        });
    }
    // ====== END SMART BUTTON LOGIC ======

    function markFormAsDirty() {
        if (!isFormDirty) {
            isFormDirty = true;
        }
    }

    // Deteksi perubahan pada semua input
    if (form) {
        form.querySelectorAll("input, select, textarea").forEach((element) => {
            // Skip hidden inputs dan submit buttons
            if (element.type === "hidden" || element.type === "submit") return;

            element.addEventListener("input", markFormAsDirty);
            element.addEventListener("change", markFormAsDirty);
        });

        // Reset status dirty saat form berhasil disubmit
        form.addEventListener("submit", function () {
            isFormDirty = false;
        });
    }

    // Konfirmasi saat klik tombol kembali — gunakan modal custom
    // NOTE: native browser unload prompts (tab close / refresh) cannot be fully
    // replaced by a custom modal. We removed the native beforeunload prompt so
    // internal navigation shows a modal UX instead. Closing the tab or refreshing
    // will NOT show this modal (browser limitation).
    const unsavedModalEl = document.getElementById("unsavedChangesModal");
    let unsavedModal = null;
    let pendingNavigation = null;
    if (unsavedModalEl && typeof bootstrap !== "undefined") {
        unsavedModal = new bootstrap.Modal(unsavedModalEl, {
            backdrop: "static",
            keyboard: false,
        });
    }

    function openUnsavedModal(href) {
        // Normalize href: if undefined/empty -> null
        pendingNavigation = href && href !== "" ? href : null;
        if (unsavedModal) {
            unsavedModal.show();
        } else {
            // If bootstrap not available, navigate immediately if we have a URL,
            // otherwise try to go back in history as a fallback.
            if (pendingNavigation) {
                window.location.href = pendingNavigation;
            } else if (window.history && window.history.length > 1) {
                window.history.back();
            }
        }
    }

    // tombol kembali di header/layout
    if (btnKembali) {
        btnKembali.addEventListener("click", function (e) {
            if (isFormDirty) {
                e.preventDefault();
                window
                    .confirmAction(
                        "Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?",
                        "Konfirmasi",
                        "Ya, tinggalkan",
                        "Batal"
                    )
                    .then((result) => {
                        if (result.isConfirmed) {
                            isFormDirty = false; // Reset flag sebelum redirect
                            window.location.href = btnKembali.href;
                        }
                    });
            }
        });
    }

    // Intercept link di sidebar/menu (hanya yang ada di sidebar) — show modal
    const sidebar = document.querySelector(".sidebar");
    if (sidebar) {
        sidebar.querySelectorAll("a[href]").forEach((link) => {
            // Skip link yang ada di dalam form
            if (link.closest("form")) return;

            link.addEventListener("click", function (e) {
                if (isFormDirty) {
                    e.preventDefault();

                    window
                        .confirmAction(
                            "Perubahan atau isian yang terjadi belum tersimpan. Apakah Anda yakin ingin meninggalkan halaman?",
                            "Konfirmasi",
                            "Ya, tinggalkan",
                            "Batal"
                        )
                        .then((result) => {
                            if (result.isConfirmed) {
                                isFormDirty = false; // Reset flag sebelum redirect
                                window.location.href = link.href;
                            }
                        });
                }
            });
        });
    }

    const rupiahInputs = document.querySelectorAll(".rupiah-mask");

    function formatRupiah(angka) {
        if (!angka) return "";
        // remove non-digits
        let number_string = String(angka).replace(/[^0-9]/g, "");
        if (number_string === "") return "";
        return number_string.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    rupiahInputs.forEach(function (input) {
        // initial format
        input.value = formatRupiah(input.value);

        input.addEventListener("input", function (e) {
            const pos = this.selectionStart;
            this.value = formatRupiah(this.value);
            // best-effort: attempt to keep cursor at end
            this.selectionStart = this.selectionEnd = this.value.length;
        });
        input.addEventListener("blur", function () {
            this.value = formatRupiah(this.value);
        });
    });

    if (!form) return;

    // Validasi form
    form.addEventListener("submit", function (e) {
        const actionInput = form.querySelector('input[name="action"]');
        const action = actionInput?.value || "save";

        // strip formatting to plain digits before submit
        rupiahInputs.forEach(function (input) {
            input.value = input.value ? input.value.replace(/\./g, "") : "";
        });

        // Reset semua error state
        form.querySelectorAll(".required-field").forEach((field) => {
            if (window.FormValidator) {
                FormValidator.clearValidation(field);
            } else {
                field.classList.remove("is-invalid");
            }
        });

        // Jika action adalah draft, skip validasi
        if (action === "draft") {
            return true;
        }

        // Validasi untuk action save/archive
        let isValid = true;
        const errors = [];

        // Helper function untuk set invalid
        function setFieldInvalid(field) {
            if (window.FormValidator) {
                FormValidator.setInvalid(field);
            } else {
                field.classList.add("is-invalid");
            }
        }

        // Validasi Nama Item
        const namaField = form.querySelector('[name="nama_item"]');
        const nama = namaField?.value.trim() || "";
        if (!nama) {
            setFieldInvalid(namaField);
            isValid = false;
        }

        // Validasi Kategori
        const kategoriField = form.querySelector('[name="kategori_id"]');
        const kategori = kategoriField?.value || "";
        if (!kategori) {
            setFieldInvalid(kategoriField);
            isValid = false;
        }

        // Validasi Kode SKU
        const kodeSkuField = form.querySelector('[name="kode_sku"]');
        const kodeSku = kodeSkuField?.value.trim() || "";
        if (!kodeSku) {
            setFieldInvalid(kodeSkuField);
            isValid = false;
        }

        // Validasi Deskripsi Produk
        const deskripsiField = form.querySelector('[name="deskripsi_produk"]');
        const deskripsi = deskripsiField?.value.trim() || "";
        if (!deskripsi) {
            setFieldInvalid(deskripsiField);
            isValid = false;
        }

        // Validasi Harga Jual
        const hargaJualField = form.querySelector('[name="harga_jual"]');
        const hargaJual = hargaJualField?.value.trim() || "";
        if (
            !hargaJual ||
            !/^\d+$/.test(hargaJual) ||
            parseInt(hargaJual) <= 0
        ) {
            setFieldInvalid(hargaJualField);
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll ke error pertama
            const firstError = form.querySelector(".is-invalid");
            if (firstError) {
                firstError.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                setTimeout(() => firstError.focus(), 300);
            }
            return false;
        }
    });

    // Real-time validation untuk required fields
    form.querySelectorAll(".required-field").forEach((field) => {
        field.addEventListener("blur", function () {
            if (this.value.trim() === "") {
                if (window.FormValidator) {
                    FormValidator.setInvalid(this);
                } else {
                    this.classList.add("is-invalid");
                }
            } else {
                if (window.FormValidator) {
                    FormValidator.clearValidation(this);
                } else {
                    this.classList.remove("is-invalid");
                }
            }
        });

        // Clear error on input
        field.addEventListener("input", function () {
            if (this.classList.contains("is-invalid")) {
                if (window.FormValidator) {
                    FormValidator.clearValidation(this);
                } else {
                    this.classList.remove("is-invalid");
                }
            }
        });

        // Validasi khusus untuk harga jual
        if (field.name === "harga_jual") {
            field.addEventListener("blur", function () {
                const value = this.value.replace(/\./g, "");
                if (!value || !/^\d+$/.test(value) || parseInt(value) <= 0) {
                    if (window.FormValidator) {
                        FormValidator.setInvalid(
                            this,
                            "Harga Jual wajib diisi dan harus berupa angka lebih dari 0"
                        );
                    } else {
                        this.classList.add("is-invalid");
                    }
                } else {
                    if (window.FormValidator) {
                        FormValidator.clearValidation(this);
                    } else {
                        this.classList.remove("is-invalid");
                    }
                }
            });
        }
    });

    // Modal confirm button: jika user pilih 'Tinggalkan Halaman'
    const unsavedConfirmBtn = document.getElementById("unsavedConfirmBtn");
    function closeUnsavedModal() {
        if (unsavedModal) {
            try {
                unsavedModal.hide();
            } catch (e) {
                /* ignore */
            }
        }
        pendingNavigation = null;
    }

    if (unsavedConfirmBtn) {
        unsavedConfirmBtn.addEventListener("click", function () {
            // User confirmed navigation: disable dirty flag and navigate
            isFormDirty = false;
            // hide modal first
            closeUnsavedModal();
            if (pendingNavigation) {
                // navigate to stored pending URL
                window.location.href = pendingNavigation;
            } else if (window.history && window.history.length > 1) {
                // fallback: go back to previous page
                window.history.back();
            }
        });
    }
});
