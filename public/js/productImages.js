document.addEventListener("DOMContentLoaded", () => {
    const imageGrid = document.getElementById("image-grid");
    const inputImages = document.getElementById("images");

    let newFiles = []; // file baru
    let oldFiles = window.existingImages || []; // gambar lama (dari DB)

    // =============== RENDER ALL ===============
    function renderGrid() {
        imageGrid.innerHTML = "";

        // 1. gambar lama
        oldFiles.forEach((img) => {
            const box = renderImageBox(img.url, () => removeOld(img.id));
            imageGrid.appendChild(box);
        });

        // 2. gambar baru
        newFiles.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const box = renderImageBox(url, () => removeNew(idx));
            imageGrid.appendChild(box);
        });
    }

    // =============== BUAT KOTAK GAMBAR ===============
    function renderImageBox(url, onRemove) {
        const wrapper = document.createElement("div");
        wrapper.className = "position-relative";
        wrapper.style.width = "120px";
        wrapper.style.height = "120px";

        // Thumbnail image
        const img = document.createElement("img");
        img.src = url;
        img.className = "img-thumbnail";
        img.style.width = "120px";
        img.style.height = "120px";
        img.style.objectFit = "cover";
        img.style.cursor = "pointer";

        // =====================================================
        // FULL IMAGE VIEWER (klik thumbnail)
        // =====================================================
        img.addEventListener("click", function () {
            const modalImage = document.getElementById("modalImage");
            modalImage.src = url;

            const myModal = new bootstrap.Modal(
                document.getElementById("imageModal")
            );
            myModal.show();
        });

        // Tombol remove
        const btn = document.createElement("button");
        btn.innerHTML = "×";
        btn.type = "button";
        btn.className = "btn btn-danger btn-sm position-absolute top-0 end-0";
        btn.style.borderRadius = "50%";
        btn.style.padding = "0 7px";
        btn.style.transform = "translate(30%, -30%)";
        btn.addEventListener("click", function (e) {
            e.stopPropagation(); // ⛔ supaya klik tombol X tidak trigger full image
            onRemove();
        });

        wrapper.appendChild(img);
        wrapper.appendChild(btn);
        return wrapper;
    }

    // =============== REMOVE OLD ===============
    function removeOld(id) {
        oldFiles = oldFiles.filter((x) => x.id != id);

        const hidden = document.querySelector(`.remove-input-${id}`);
        hidden.value = id;

        renderGrid();
    }

    // =============== REMOVE NEW ===============
    function removeNew(idx) {
        newFiles.splice(idx, 1);
        syncToInput();
        renderGrid();
    }

    // =============== SYNC INPUT FILES ===============
    function syncToInput() {
        let dt = new DataTransfer();
        newFiles.forEach((f) => dt.items.add(f));
        inputImages.files = dt.files;
    }

    // =============== HANDLE FILE INPUT ===============
    inputImages.addEventListener("change", function () {
        for (let i = 0; i < this.files.length; i++) {
            newFiles.push(this.files[i]);
        }
        syncToInput();
        renderGrid();
    });

    // initial render (gambar lama)
    renderGrid();
});
