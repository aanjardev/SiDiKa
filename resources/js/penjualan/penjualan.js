import Checkout from "./checkout.js";
import Totals from "./totals.js";
import ProductSelector from "./product-selector.js";
import CustomerModal from "../utils/modal-customer.js";
import { maskRupiah } from "../utils/rupiah.js";
import { syncHiddenRaw } from "../utils/form.js";

document.addEventListener("DOMContentLoaded", () => {

    const productDataEl = document.getElementById("produk-data-json");
    const productCatalog = productDataEl ? JSON.parse(productDataEl.textContent || "[]") : [];

    const checkout = new Checkout({
        itemsInput: document.getElementById("itemsInput"),
        tableBody: document.getElementById("tableItemsBody"),
        products: productCatalog
    });

    const totals = new Totals({
        checkout,
        elements: {
            subtotal: document.getElementById("lineSubtotal"),
            total: document.getElementById("subtotalValue"),
            submit: document.querySelector("#formPenjualan button[type='submit']"),

            diskon: document.querySelector("input[name='diskon']"),
            depresiasi: document.querySelector("input[name='depresiasi']"),
            biaya: document.querySelector("input[name='biaya_tambahan']"),

            diskonRow: document.getElementById("lineDiskonRow"),
            depresiasiRow: document.getElementById("lineDepresiasiRow"),
            biayaRow: document.getElementById("lineBiayaRow")
        }
    });

    checkout.render();
    totals.recalc();

    new ProductSelector({
        select: document.getElementById("produkBaru"),
        qtyInput: document.getElementById("qtyProdukBaru"),
        stockInfo: document.getElementById("infoStokProduk")
    });

    new CustomerModal();

    // Tambah item dari modal
    document.getElementById("formTambahItem")?.addEventListener("submit", e => {
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
        bootstrap.Modal.getInstance(modalEl)?.hide();

        if (qtyInput) qtyInput.value = "1";
    });

    // Qty buttons
    document.getElementById("tableItemsBody").addEventListener("click", e => {
        const inc = e.target.closest(".btn-qty-inc");
        const dec = e.target.closest(".btn-qty-dec");

        if (inc) checkout.update(inc.dataset.productId, 1);
        if (dec) checkout.update(dec.dataset.productId, -1);

        totals.recalc();
    });

    // Rupiah inputs
    document.querySelectorAll(".rupiah-mask").forEach(input => {
        maskRupiah(input);
        input.addEventListener("input", () => {
            maskRupiah(input);
            totals.recalc();
        });
    });

    // Before submit → sync hidden raw values
    document.getElementById("formPenjualan").addEventListener("submit", () => {
        document.querySelectorAll(".rupiah-mask").forEach(syncHiddenRaw);
    });

});
