export default class CustomerModal {
    constructor({ onSuccess, storeUrl = "/admin/customers" }) {
        this.btn = document.getElementById("btnSimpanCustomer");
        this.form = document.getElementById("formTambahCustomer");
        this.modalEl = document.getElementById("modalTambahCustomer");
        this.modal = this.modalEl ? new bootstrap.Modal(this.modalEl) : null;
        this.onSuccess = onSuccess;
        this.storeUrl = storeUrl;
        this.requiredIds = ["customer_nama_modal", "customer_no_telp_modal", "customer_jenis_kelamin_modal"];

        this.init();
    }

    init() {
        if (!this.btn || !this.form) return;

        this.requiredIds.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const eventType = el.tagName === "SELECT" ? "change" : "input";
            el.addEventListener(eventType, () => {
                if (el.value) {
                    el.classList.remove("is-invalid");
                }
            });
        });

        this.btn.addEventListener("click", (e) => {
            e.preventDefault();
            this.save();
        });
    }

    async save() {
        if (!this.form || !this.btn) return;

        // simple required validation similar to old inline logic
        const invalid = [];
        this.requiredIds.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const isEmpty = !el.value;
            el.classList.toggle("is-invalid", isEmpty);
            if (isEmpty) invalid.push(id);
        });
        if (invalid.length) {
            const firstInvalid = document.getElementById(invalid[0]);
            firstInvalid?.focus();
            this.showAlert(
                "Lengkapi semua kolom bertanda * sebelum menyimpan customer.",
                "warning"
            );
            return;
        }

        const data = Object.fromEntries(new FormData(this.form).entries());

        this.btn.disabled = true;
        this.btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;

        let result;
        let responseOk = false;
        let handled = false;

        try {
            const res = await fetch(this.storeUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(data)
            });
            responseOk = res.ok;
            result = await res.json();
        } catch (error) {
            this.showAlert("Terjadi kesalahan jaringan. Silakan coba lagi.");
            handled = true;
        }

        if (!handled && responseOk && result?.success) {
            if (this.onSuccess) this.onSuccess(result.customer);

            this.form.reset();
            this.requiredIds.forEach((id) => {
                document.getElementById(id)?.classList.remove("is-invalid");
            });
            this.hideModalSafely();
            this.showAlert("Customer berhasil disimpan.", "success");
        } else if (!handled && result?.errors) {
            const messages = Object.values(result.errors)
                .flat()
                .filter(Boolean);
            const message = messages.length ? messages[0] : "Gagal menyimpan customer.";
            this.showAlert(message, "error");
        } else if (!handled && result?.message) {
            this.showAlert(result.message, "error");
        } else if (!handled) {
            this.showAlert("Gagal menyimpan customer. Silakan cek data Anda.", "error");
        }

        this.btn.disabled = false;
        this.btn.innerHTML = `Simpan`;
    }

    hideModalSafely() {
        if (!this.modalEl) return;
        const instance =
            bootstrap.Modal.getInstance(this.modalEl) ||
            this.modal ||
            new bootstrap.Modal(this.modalEl);
        instance?.hide();
        setTimeout(() => {
            document.querySelectorAll(".modal-backdrop").forEach((el) => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.removeProperty("overflow");
            document.body.style.removeProperty("padding-right");
        }, 350);
    }

    showAlert(message, type = "error") {
        const uiTypeMap = {
            success: "success",
            warning: "warning",
            info: "info",
            error: "danger"
        };

        const titleMap = {
            success: "Berhasil",
            warning: "Perhatian",
            info: "Informasi",
            error: "Terjadi Kesalahan"
        };

        if (window.UiAlert?.push) {
            window.UiAlert.push({
                type: uiTypeMap[type] || "info",
                title: titleMap[type] || "Informasi",
                message,
                autoDismiss: type === "success" || type === "info"
            });
            return;
        }

        const fallbackMap = {
            success: window.showSuccess,
            warning: window.showWarning,
            info: window.showInfo,
            error: window.showError
        };

        if (typeof fallbackMap[type] === "function") {
            fallbackMap[type](message);
            return;
        }

        if (typeof fallbackMap.error === "function") {
            fallbackMap.error(message);
            return;
        }

        alert(message);
    }
}
