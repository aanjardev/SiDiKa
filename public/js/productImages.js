(() => {
    class ProductImageUploader {
        constructor(config) {
            this.grid = document.getElementById(config.gridId);
            this.form = config.formId ? document.getElementById(config.formId) : null;
            this.hiddenInput = config.hiddenInputId ? document.getElementById(config.hiddenInputId) : null;
            this.hiddenMainInput = config.hiddenMainInputId ? document.getElementById(config.hiddenMainInputId) : null;
            this.saveBtn = config.saveButtonId ? document.getElementById(config.saveButtonId) : null;
            this.statusElement = config.statusId ? document.getElementById(config.statusId) : null;
            this.removalInputContainer = config.removalInputContainerId
                ? document.getElementById(config.removalInputContainerId)
                : null;

            this.maxBoxes = parseInt(config.maxBoxes, 10) || 10;
            this.maxFileSize = parseInt(config.maxFileSize, 10) || 5 * 1024 * 1024;
            this.allowMainChoice = Boolean(config.allowMainChoice);
            this.requireFilesOnSubmit = Boolean(config.requireFilesOnSubmit);
            this.removalInputName = config.removalInputName || 'remove_images[]';
            this.existingImages = Array.isArray(config.existingImages) ? config.existingImages : [];
            this.requireAtLeastOne = Boolean(config.requireAtLeastOne);
            this.initialEmptyBoxes = parseInt(config.initialEmptyBoxes, 10) || 1;

            this.queueIndex = 0;
            this.queuedFiles = {};
            this.removedExistingIds = new Set();

            this.initialized = false;
        }

        init() {
            if (this.initialized) {
                return;
            }

            if (!this.grid || !this.form || !this.hiddenInput) {
                return;
            }

            this.initialized = true;

            this.renderExistingImages();
            this.ensureBoxes();

            if (this.saveBtn) {
                this.saveBtn.addEventListener('click', (event) => this.handleSaveButton(event));
            }

            this.form.addEventListener('submit', (event) => this.handleFormSubmit(event));
        }

        renderExistingImages() {
            if (!Array.isArray(this.existingImages) || !this.existingImages.length) {
                return;
            }

            this.existingImages.forEach((image) => {
                if (!image || !image.url) {
                    return;
                }
                const box = document.createElement('div');
                box.className = 'upload-box has-image existing-image';
                box.dataset.existingId = image.id || '';

                const mainChoiceMarkup =
                    this.allowMainChoice && image.id
                        ? `
                    <div class="main-choice">
                        <label class="form-check-label">
                            <input type="radio" name="main_image_choice" value="existing_${image.id}" class="form-check-input mt-0" ${
                                image.is_main ? 'checked' : ''
                            }>
                            <span>Utama</span>
                        </label>
                    </div>
                `
                        : '';

                box.innerHTML = `
                    <div class="preview">
                        <img src="${image.url}" alt="Foto produk">
                    </div>
                    <div class="controls">
                        <button type="button" class="btn btn-danger btn-remove-existing" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    ${mainChoiceMarkup}
                `;

                const removeBtn = box.querySelector('.btn-remove-existing');
                if (removeBtn) {
                    removeBtn.addEventListener('click', (event) => {
                        event.stopPropagation();
                        this.markExistingForRemoval(image.id, box);
                    });
                }

                if (this.allowMainChoice) {
                    box.addEventListener('click', (event) => {
                        if (event.target.closest('.controls') || event.target.closest('.main-choice') || event.target.tagName === 'INPUT') {
                            return;
                        }
                        const radio = box.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                        }
                    });
                }

                this.grid.appendChild(box);
            });
        }

        markExistingForRemoval(imageId, box) {
            if (!imageId || this.removedExistingIds.has(imageId)) {
                box.remove();
                this.ensureBoxes();
                return;
            }

            if (this.requireAtLeastOne) {
                const remaining = this.getTotalImagesCount({ excludeExistingId: imageId });
                if (remaining < 1) {
                    this.showStatus('Minimal harus ada 1 foto produk', 'error');
                    return;
                }
            }

            this.removedExistingIds.add(imageId);

            if (this.removalInputContainer) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = this.removalInputName;
                input.value = imageId;
                this.removalInputContainer.appendChild(input);
            }

            box.remove();
            this.ensureBoxes();
        }

        makeEmptyBox() {
            const idx = ++this.queueIndex;
            const box = document.createElement('div');
            box.className = 'upload-box';
            box.dataset.boxIndex = idx;

            box.innerHTML = `
                <input type="file" accept="image/*" class="d-none file-input" data-index="${idx}" dusk="file-input-${idx}">
                <div class="empty-state">
                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                    <div style="font-size: 0.75rem; font-weight: 500;">Klik Upload</div>
                </div>
                <div class="preview d-none"></div>
                <div class="controls d-none">
                    <button type="button" class="btn btn-danger btn-remove-queue" data-index="${idx}" title="Hapus">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                ${
                    this.allowMainChoice
                        ? `
                    <div class="main-choice d-none">
                        <label class="form-check-label">
                            <input type="radio" name="main_image_choice" value="new_${idx}" class="form-check-input mt-0">
                            <span>Utama</span>
                        </label>
                    </div>
                `
                        : ''
                }
            `;

            const fileInput = box.querySelector('.file-input');
            const removeBtn = box.querySelector('.btn-remove-queue');

            box.addEventListener('click', (event) => {
                if (event.target.closest('.controls') || event.target.closest('.main-choice')) {
                    return;
                }
                fileInput.click();
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files[0];
                if (!file) {
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    this.showStatus('File harus berupa gambar', 'error');
                    fileInput.value = '';
                    return;
                }

                if (file.size > this.maxFileSize) {
                    this.showStatus('Ukuran file maksimal 5MB', 'error');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    const preview = box.querySelector('.preview');
                    preview.innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                    preview.classList.remove('d-none');
                    box.querySelector('.empty-state').classList.add('d-none');
                    box.querySelector('.controls').classList.remove('d-none');
                    const mainChoice = box.querySelector('.main-choice');
                    if (mainChoice) {
                        mainChoice.classList.remove('d-none');
                    }
                    box.classList.add('has-image');

                    this.queuedFiles[idx] = file;
                    this.updateSaveButton();
                    this.ensureBoxes();
                };
                reader.readAsDataURL(file);
            });

            removeBtn.addEventListener('click', (event) => {
                event.stopPropagation();

                if (this.requireAtLeastOne) {
                    const remaining = this.getTotalImagesCount({ excludeNewIndex: idx });
                    if (remaining < 1) {
                        this.showStatus('Minimal harus ada 1 foto produk', 'error');
                        return;
                    }
                }

                const radio = box.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = false;
                }

                delete this.queuedFiles[idx];
                box.remove();
                this.updateSaveButton();
                this.ensureBoxes();
            });

            return box;
        }

        getRemainingExistingCount(options = {}) {
            let count = this.existingImages.length - this.removedExistingIds.size;
            if (options.excludeExistingId && !this.removedExistingIds.has(options.excludeExistingId)) {
                count -= 1;
            }
            return Math.max(count, 0);
        }

        getQueuedCount(options = {}) {
            const excludeIndex = options.excludeNewIndex != null ? String(options.excludeNewIndex) : null;
            return Object.keys(this.queuedFiles).reduce((total, key) => {
                if (excludeIndex && String(key) === excludeIndex) {
                    return total;
                }
                return total + 1;
            }, 0);
        }

        getTotalImagesCount(options = {}) {
            return this.getRemainingExistingCount(options) + this.getQueuedCount(options);
        }

        ensureBoxes() {
            if (!this.grid) {
                return;
            }

            const filledBoxes = this.grid.querySelectorAll('.upload-box.has-image').length;
            let emptyBoxes = Array.from(this.grid.querySelectorAll('.upload-box:not(.has-image)'));

            if (filledBoxes >= this.maxBoxes) {
                emptyBoxes.forEach((box) => box.remove());
                return;
            }

            let desiredEmpty = Math.max(this.initialEmptyBoxes - filledBoxes, 0);
            if (desiredEmpty === 0) {
                desiredEmpty = 1;
            }
            desiredEmpty = Math.min(desiredEmpty, Math.max(this.maxBoxes - filledBoxes, 0));

            while (emptyBoxes.length < desiredEmpty && filledBoxes + emptyBoxes.length < this.maxBoxes) {
                const newBox = this.makeEmptyBox();
                this.grid.appendChild(newBox);
                emptyBoxes.push(newBox);
            }

            if (emptyBoxes.length > desiredEmpty) {
                emptyBoxes
                    .slice(desiredEmpty)
                    .forEach((box) => box.remove());
            }
        }

        updateSaveButton() {
            if (!this.saveBtn) {
                return;
            }

            const queueLength = Object.keys(this.queuedFiles).length;
            this.saveBtn.disabled = queueLength === 0;

            if (queueLength > 0) {
                this.saveBtn.innerHTML = `<i class="fas fa-save"></i> Simpan (${queueLength})`;
                return;
            }

            this.saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan';
        }

        populateHiddenInputs() {
            if (!this.hiddenInput) {
                return;
            }

            const dt = new DataTransfer();
            Object.values(this.queuedFiles).forEach((file) => dt.items.add(file));
            this.hiddenInput.files = dt.files;

            if (this.hiddenMainInput) {
                const selected = this.form.querySelector('input[name="main_image_choice"]:checked');
                this.hiddenMainInput.value = selected ? selected.value : '';
            }
        }

        getQueuedFiles() {
            return Object.values(this.queuedFiles);
        }

        handleSaveButton(event) {
            event.preventDefault();

            if (this.requireFilesOnSubmit && this.getQueuedFiles().length === 0) {
                this.showStatus('Tidak ada gambar untuk disimpan', 'error');
                return;
            }

            this.populateHiddenInputs();
            this.disableSaveButton();
            this.form.submit();
        }

        handleFormSubmit(event) {
            this.populateHiddenInputs();

            const hasQueued = this.getQueuedFiles().length > 0;

            if (this.requireFilesOnSubmit && !hasQueued && !this.removedExistingIds.size) {
                event.preventDefault();
                this.showStatus('Tidak ada gambar untuk disimpan', 'error');
                return;
            }

            if (this.requireAtLeastOne && this.getTotalImagesCount() < 1) {
                event.preventDefault();
                this.showStatus('Minimal harus ada 1 foto produk', 'error');
                return;
            }

            this.disableSaveButton();
        }

        disableSaveButton() {
            if (!this.saveBtn) {
                return;
            }

            this.saveBtn.disabled = true;
            this.saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        }

        showStatus(message, type = 'success') {
            if (!this.statusElement) {
                return;
            }

            this.statusElement.className = `upload-status ${type}`;
            this.statusElement.textContent = message;
            this.statusElement.style.display = 'block';

            window.clearTimeout(this.statusTimeout);
            this.statusTimeout = window.setTimeout(() => {
                this.statusElement.style.display = 'none';
            }, 3000);
        }
    }

    function boot() {
        const configs = window.productImageConfigs || [];
        configs.forEach((config) => {
            try {
                new ProductImageUploader(config).init();
            } catch (error) {
                console.error('ProductImageUploader init failed', error);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
