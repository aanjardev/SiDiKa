import Checkout from "./checkout.js";
import Totals from "./totals.js";
import ProductSelector from "./product-selector.js";
import CustomerSearch from "../utils/customer-search.js";
import CustomerModal from "../utils/modal-customer.js";
import { maskRupiah } from "../utils/rupiah.js";
import { syncHiddenRaw } from "../utils/form.js";

document.addEventListener("DOMContentLoaded", () => {
    const productDataEl = document.getElementById("produk-data-json");
    const productCatalog = productDataEl
        ? JSON.parse(productDataEl.textContent || "[]")
        : [];

    const checkout = new Checkout({
        itemsInput: document.getElementById("itemsInput"),
        tableBody: document.getElementById("tableItemsBody"),
        products: productCatalog,
    });

    const totals = new Totals({
        checkout,
        elements: {
            subtotal: document.getElementById("lineSubtotal"),
            total: document.getElementById("subtotalValue"),
            submit: document.querySelector(
                "#formPenjualan button[type='submit']"
            ),

            diskon: document.querySelector("input[name='diskon']"),
            depresiasi: document.querySelector("input[name='depresiasi']"),
            biaya: document.querySelector("input[name='biaya_tambahan']"),

            diskonRow: document.getElementById("lineDiskonRow"),
            // depresiasiRow tidak digunakan karena depresiasi hanya info, tidak mengurangi total
            biayaRow: document.getElementById("lineBiayaRow")
        }
    });

    checkout.render();
    totals.recalc();

    new ProductSelector({
        select: document.getElementById("produkBaru"),
        qtyInput: document.getElementById("qtyProdukBaru"),
        stockInfo: document.getElementById("infoStokProduk"),
        qtyWrapper: document.getElementById("qtyProdukWrapper"),
        searchInput: document.getElementById("produkSearchInput"),
    });

    const customerSearchInput = document.getElementById("customer_search");
    const customerIdInput = document.getElementById("customer_id");
    const customerSuggestions = document.getElementById("customer_suggestions");
    const customerSearchError = document.getElementById(
        "customer_search_error"
    );

    const clearCustomerError = () => {
        if (customerSearchInput) {
            if (window.FormValidator) {
                FormValidator.clearValidation(customerSearchInput);
            } else {
                customerSearchInput.classList.remove("is-invalid");
            }
        }
        if (customerSearchError)
            customerSearchError.classList.remove("d-block");
    };

    new CustomerSearch({
        input: customerSearchInput,
        hiddenInput: customerIdInput,
        suggestions: customerSuggestions,
        searchUrl: customerSearchInput?.dataset.searchUrl,
        onSelect: clearCustomerError,
        onInput: clearCustomerError,
    });

    new CustomerModal({
        onSuccess: (customer) => {
            if (customerSearchInput && customerIdInput) {
                customerSearchInput.value = `${customer.nama} (${customer.no_telp})`;
                customerIdInput.value = customer.id;
                clearCustomerError();
            }
            if (customerSuggestions) customerSuggestions.style.display = "none";
        },
    });

    const hideModalSafely = (modalEl) => {
        if (!modalEl) return;
        const instance =
            bootstrap.Modal.getInstance(modalEl) ||
            new bootstrap.Modal(modalEl);
        instance.hide();
        setTimeout(() => {
            document
                .querySelectorAll(".modal-backdrop")
                .forEach((el) => el.remove());
            document.body.classList.remove("modal-open");
            document.body.style.removeProperty("overflow");
            document.body.style.removeProperty("padding-right");
        }, 300);
    };

    // Tambah item dari modal
    document
        .getElementById("formTambahItem")
        ?.addEventListener("submit", (e) => {
            e.preventDefault();

            const select = document.getElementById("produkBaru");
            const qtyInput = document.getElementById("qtyProdukBaru");
            const productId = select?.value;
            let qty = Number(qtyInput?.value || 0);

            if (!productId || qty < 1) return;

            const limit = checkout.getLimitQty(productId);
            if (limit === 0) return;
            qty = limit === Infinity ? qty : Math.min(qty, limit);

            checkout.add(productId, qty);
            totals.recalc();

            const modalEl = document.getElementById("modalTambahItem");
            hideModalSafely(modalEl);

            if (qtyInput) qtyInput.value = "1";
        });

    // Qty buttons
    document.getElementById("tableItemsBody").addEventListener("click", (e) => {
        const inc = e.target.closest(".btn-qty-inc");
        const dec = e.target.closest(".btn-qty-dec");

        if (inc) checkout.update(inc.dataset.productId, 1);
        if (dec) checkout.update(dec.dataset.productId, -1);

        totals.recalc();
    });

    // Rupiah inputs
    document.querySelectorAll(".rupiah-mask").forEach((input) => {
        maskRupiah(input);
        input.addEventListener("input", () => {
            maskRupiah(input);
            totals.recalc();
        });
    });

    // Before submit → sync hidden raw values
    document.getElementById("formPenjualan").addEventListener("submit", (e) => {
        if (customerSearchInput && customerIdInput && !customerIdInput.value) {
            e.preventDefault();
            if (window.FormValidator) {
                FormValidator.setInvalid(
                    customerSearchInput,
                    "Customer wajib dipilih."
                );
            } else {
                customerSearchInput.classList.add("is-invalid");
            }
            if (customerSearchError) {
                customerSearchError.classList.add("d-block");
                customerSearchError.textContent = "Customer wajib dipilih.";
            }
            customerSearchInput.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            setTimeout(() => customerSearchInput.focus(), 300);
            return;
        }

        document.querySelectorAll(".rupiah-mask").forEach(syncHiddenRaw);
    });
});
