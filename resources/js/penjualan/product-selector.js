export default class ProductSelector {
    constructor({ select, qtyInput, stockInfo }) {
        this.select = select;
        this.qtyInput = qtyInput;
        this.stockInfo = stockInfo;

        this.bind();
    }

    bind() {
        this.select?.addEventListener("change", () => {
            const option = this.select.options[this.select.selectedIndex];
            const stock = option?.dataset?.stock;

            if (!stock) {
                this.stockInfo.classList.add("d-none");
                this.stockInfo.textContent = "";
                return;
            }

            this.stockInfo.innerHTML = `
                <i class="fa-solid fa-circle-info me-1"></i>
                Stok tersedia: <strong>${stock}</strong>`;

            this.stockInfo.classList.remove("d-none");
        });
    }
}
