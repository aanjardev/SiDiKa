document.addEventListener("DOMContentLoaded", function () {

    const uploadGrid = document.getElementById("upload-grid");
    const hiddenInput = document.getElementById("image-input-hidden");

    const MAX_FILES = 10;
    const MAX_SIZE = 5 * 1024 * 1024;

    let queuedFiles = {};   // index → File
    let indexCounter = 0;

    function createUploadBox(idx) {
        const box = document.createElement("div");
        box.className = "upload-box";
        box.dataset.index = idx;

        box.innerHTML = `
            <input type="file" accept="image/*" class="d-none file-input">
            <div class="empty-state">
                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                <div style="font-size:0.75rem;font-weight:500;">Klik Upload</div>
            </div>

            <div class="preview d-none"></div>

            <div class="controls d-none">
                <button class="btn btn-danger btn-remove-queue" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="main-choice d-none">
                <label class="form-check-label">
                    <input type="radio" name="main_image_choice" value="new_${idx}" class="form-check-input mt-0">
                    <span>Utama</span>
                </label>
            </div>
        `;

        const input = box.querySelector(".file-input");
        const removeBtn = box.querySelector(".btn-remove-queue");

        // Klik untuk buka file
        box.addEventListener("click", function (e) {
            if (e.target.closest(".controls") || e.target.closest(".main-choice")) return;
            input.click();
        });

        // Input file change
        input.addEventListener("change", function () {
            const file = this.files[0];
            if (!file) return;

            if (!file.type.startsWith("image/")) {
                alert("File harus berupa gambar.");
                return;
            }
            if (file.size > MAX_SIZE) {
                alert("Ukuran gambar maksimal 5MB.");
                return;
            }

            previewFile(file, box, idx);
            queuedFiles[idx] = file;

            updateHiddenInput();
            ensureAvailableBox();
        });

        // Hapus file
        removeBtn.addEventListener("click", function (e) {
            e.stopPropagation();

            delete queuedFiles[idx];
            box.remove();

            updateHiddenInput();
            ensureAvailableBox();
        });

        return box;
    }

    function previewFile(file, box, idx) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = box.querySelector(".preview");
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;

            box.querySelector(".preview").classList.remove("d-none");
            box.querySelector(".empty-state").classList.add("d-none");
            box.querySelector(".controls").classList.remove("d-none");
            box.querySelector(".main-choice").classList.remove("d-none");

            box.classList.add("has-image");
        };
        reader.readAsDataURL(file);
    }

    function updateHiddenInput() {
        const dataTransfer = new DataTransfer();

        Object.values(queuedFiles).forEach(file => {
            dataTransfer.items.add(file);
        });

        hiddenInput.files = dataTransfer.files;
    }

    function ensureAvailableBox() {
        const current = uploadGrid.querySelectorAll(".upload-box").length;
        const filled = uploadGrid.querySelectorAll(".upload-box.has-image").length;

        if (current < MAX_FILES && current === filled) {
            uploadGrid.appendChild(createUploadBox(++indexCounter));
        }

        if (current === 0) {
            uploadGrid.appendChild(createUploadBox(++indexCounter));
        }
    }

    ensureAvailableBox();
});
