export default class CustomerModal {
    constructor({ onSuccess, storeUrl = "/admin/customers" }) {
        this.btn = document.getElementById("btnSimpanCustomer");
        this.form = document.getElementById("formTambahCustomer");
        this.modalEl = document.getElementById("modalTambahCustomer");
        this.modal = this.modalEl ? new bootstrap.Modal(this.modalEl) : null;
        this.onSuccess = onSuccess;
        this.storeUrl = storeUrl;
        this.requiredIds = [
            "customer_nama_modal",
            "customer_no_telp_modal",
            "customer_jenis_kelamin_modal",
        ];

        this.init();
    }

    init() {
        if (!this.btn || !this.form) return;

        // Autofocus pada input pertama saat modal ditampilkan
        if (this.modalEl) {
            this.modalEl.addEventListener("shown.bs.modal", () => {
                const firstInput = document.getElementById(this.requiredIds[0]);
                if (firstInput) {
                    setTimeout(() => {
                        firstInput.focus();
                    }, 100);
                }
            });
        }

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

        // Validate using FormValidator if available, otherwise use simple validation
        const invalid = [];
        this.requiredIds.forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;
            const isEmpty = !el.value;

            if (isEmpty) {
                if (window.FormValidator) {
                    FormValidator.setInvalid(el);
                } else {
                    el.classList.add("is-invalid");
                }
                invalid.push(id);
            } else {
                if (window.FormValidator) {
                    FormValidator.clearValidation(el);
                } else {
                    el.classList.remove("is-invalid");
                }
            }
        });

        if (invalid.length) {
            const firstInvalid = document.getElementById(invalid[0]);
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
                setTimeout(() => firstInvalid.focus(), 300);
            }
            return;
        }

        const data = Object.fromEntries(new FormData(this.form).entries());

        this.btn.disabled = true;
        this.btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Menyimpan...`;

        const res = await fetch(this.storeUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content,
            },
            body: JSON.stringify(data),
        });

        const result = await res.json();

        if (result.success) {
            if (this.onSuccess) this.onSuccess(result.customer);

            this.form.reset();
            this.requiredIds.forEach((id) => {
                document.getElementById(id)?.classList.remove("is-invalid");
            });

            if (this.modal) {
                this.modal.hide();
            }

            // Safeguard: pastikan backdrop dan state body dibersihkan
            setTimeout(() => {
                document
                    .querySelectorAll('.modal-backdrop')
                    .forEach((el) => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }, 200);
        } else {
            alert("Gagal menyimpan customer");
        }

        this.btn.disabled = false;
        this.btn.innerHTML = `Simpan`;
    }
}
