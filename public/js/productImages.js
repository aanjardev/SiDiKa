document.addEventListener("DOMContentLoaded", function () {

    const uploadGrid = document.getElementById("upload-grid");
    const addButton = document.getElementById("add-image-btn");
    const hiddenInput = document.getElementById("image-input-hidden");

    const MAX_FILES = 10;
    const MAX_SIZE = 5 * 1024 * 1024;

    let selectedFiles = [];

    function renderGrid() {
        uploadGrid.innerHTML = "";

        selectedFiles.forEach((file, index) => {
            const url = URL.createObjectURL(file);

            const box = document.createElement("div");
            box.classList.add("upload-box");
            box.innerHTML = `
                <img src="${url}">
                <button class="remove-btn" data-index="${index}">&times;</button>
                <div class="main-badge ${index === 0 ? '' : 'd-none'}">UTAMA</div>
            `;
            uploadGrid.appendChild(box);
        });
    }

    function addFiles(files) {
        for (const file of files) {
            if (selectedFiles.length >= MAX_FILES) break;
            if (!file.type.startsWith("image/")) continue;
            if (file.size > MAX_SIZE) {
                alert("Ukuran gambar maksimal 5MB.");
                continue;
            }
            selectedFiles.push(file);
        }
        renderGrid();
        syncToForm();
    }

    function syncToForm() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(f => dataTransfer.items.add(f));
        hiddenInput.files = dataTransfer.files;
    }

    uploadGrid.addEventListener("click", (e) => {
        if (e.target.classList.contains("remove-btn")) {
            const idx = e.target.dataset.index;
            selectedFiles.splice(idx, 1);
            renderGrid();
            syncToForm();
            return;
        }
    });

    addButton.addEventListener("click", () => hiddenInput.click());

    hiddenInput.addEventListener("change", function () {
        addFiles(this.files);
        hiddenInput.value = "";
    });

});
