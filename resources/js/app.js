import * as bootstrap from "bootstrap";
import debounce from "./utils/debounce.js";
import CustomerSearch from "./utils/customer-search.js";
import CustomerModal from "./utils/modal-customer.js";
import loadKategoriMap from "./utils/kategori-map.js";
import { formatRupiah, cleanNumber, maskRupiah } from "./utils/rupiah.js";
import { syncHiddenRaw } from "./utils/form.js";
import { initGlobalInputMasks } from "./utils/input-masks.js";
import ItemAPI from "./pembelian/item-api.js";
import ItemForm from "./pembelian/item-form.js";
import ItemTable from "./pembelian/item-table.js";
import DealButtons from "./pembelian/deal-buttons.js";
import Checkout from "./penjualan/checkout.js";
import ProductSelector from "./penjualan/product-selector.js";
import Totals from "./penjualan/totals.js";
import "./utils/clickable-rows.js";
import "./utils/handle-delete.js";
import "./utils/phone-input-validation.js";
import "./utils/search.js";

// Expose bootstrap globally for inline scripts and other modules that rely on window.bootstrap
if (typeof window !== "undefined") {
    window.bootstrap = bootstrap;
}

document.addEventListener("DOMContentLoaded", () => {
    initGlobalInputMasks();
});

export {
    bootstrap,
    debounce,
    CustomerSearch,
    CustomerModal,
    loadKategoriMap,
    formatRupiah,
    cleanNumber,
    maskRupiah,
    syncHiddenRaw,
    initGlobalInputMasks,
    ItemAPI,
    ItemForm,
    ItemTable,
    DealButtons,
    Checkout,
    ProductSelector,
    Totals,
};
