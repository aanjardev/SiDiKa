@extends('layouts.admin')

@section('title', 'Upload Foto Produk - ' . ($product->nama_produk ?? 'Produk'))

@push('page-actions')
    <a href="{{ route('admin.products.photos') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
@endpush

@section('content')

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <h5 class="alert-heading">Ada Kesalahan!</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    {{-- Informasi Produk --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-1">{{ $product->nama_produk }}</h5>
                <p class="text-muted mb-0">SKU: <strong>{{ $product->kode_sku }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Upload Gambar Baru --}}
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="card-title mb-1">Upload Gambar Baru</h5>
                        <small class="text-muted">Maksimal 10 gambar. Pilih gambar untuk preview, lalu klik Simpan untuk mengunggah.</small>
                    </div>
                    <button id="save-uploads" class="btn btn-primary btn-slim" disabled>
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>

                <div id="upload-grid" class="d-flex flex-wrap gap-3">
                    {{-- Upload boxes akan ditambahkan via JavaScript --}}
                </div>
                <div id="upload-status" class="mt-3"></div>
            </div>
        </div>
    </div>

    {{-- Gambar Saat Ini
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title mb-3">Gambar Saat Ini</h5>
                <div id="current-images" class="d-flex flex-wrap gap-3">
                    @forelse ($product->photos as $photo)
                    <div class="card border position-relative" style="width: 160px;" data-image-id="{{ $photo->id }}">
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="Gambar produk">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if ($photo->is_main)
                                    <span class="badge bg-primary">Utama</span>
                                    @else
                                    <span class="badge bg-secondary">Tambahan</span>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info btn-set-main" data-image-id="{{ $photo->id }}" title="Set sebagai Gambar Utama">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button class="btn btn-outline-danger remove-image" data-image-id="{{ $photo->id }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="w-100 text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada gambar yang diunggah.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div> --}}
</div>

@endsection

@push('styles')
<style>
    .btn-slim {
        height: 40px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .upload-box {
        width: 160px;
        height: 160px;
        background: #f8f9fc;
        border: 2px dashed #ced4da;
        border-radius: 8px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .upload-box:hover {
        border-color: #4e6bff;
        background: #f0f4ff;
    }

    .upload-box.has-image {
        border: 2px solid #4e6bff;
    }

    .upload-box .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
    }

    .upload-box .preview {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .upload-box .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .upload-box .controls {
        position: absolute;
        top: 4px;
        right: 4px;
        z-index: 10;
    }

    .upload-box .main-choice {
        position: absolute;
        bottom: 4px;
        left: 4px;
        z-index: 10;
        background: rgba(255, 255, 255, 0.9);
        padding: 4px 8px;
        border-radius: 4px;
    }

    .upload-box .main-choice label {
        margin: 0;
        font-size: 0.75rem;
        cursor: pointer;
    }

    .upload-status {
        padding: 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .upload-status.success {
        background: #d1e7dd;
        color: #0f5132;
    }

    .upload-status.error {
        background: #f8d7da;
        color: #842029;
    }

    #current-images .card {
        transition: transform 0.2s ease;
    }

    #current-images .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const maxBoxes = 10;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const uploadGrid = document.getElementById('upload-grid');
    const saveBtn = document.getElementById('save-uploads');
    const uploadStatus = document.getElementById('upload-status');
    const productId = '{{ $product->id }}';
    const csrf = '{{ csrf_token() }}';

    let queueIndex = 0;
    const queuedFiles = {}; // map index -> File

    // Fungsi untuk membuat upload box
    function makeEmptyBox(idx) {
        const box = document.createElement('div');
        box.className = 'upload-box';
        box.dataset.boxIndex = idx;

        box.innerHTML = `
            <input type="file" accept="image/*" class="d-none file-input" data-index="${idx}">
            <div class="empty-state">
                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                <div style="font-size: 0.75rem;">Klik untuk Upload</div>
            </div>
            <div class="preview d-none"></div>
            <div class="controls d-none">
                <button type="button" class="btn btn-sm btn-danger btn-remove-queue" data-index="${idx}" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="main-choice d-none">
                <label class="form-check-label">
                    <input type="radio" name="main_image_choice" value="new_${idx}" class="form-check-input">
                    <span style="font-size: 0.75rem;">Utama</span>
                </label>
            </div>
        `;

        const input = box.querySelector('.file-input');
        const removeBtn = box.querySelector('.btn-remove-queue');

        // Click box untuk pilih file
        box.addEventListener('click', function(e) {
            if (e.target.closest('.controls') || e.target.closest('.main-choice')) return;
            input.click();
        });

        // File input change
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;

            // Validasi tipe file
            if (!file.type.startsWith('image/')) {
                showStatus('File harus berupa gambar', 'error');
                return;
            }

            // Validasi ukuran file
            if (file.size > maxFileSize) {
                showStatus('Ukuran file maksimal 5MB', 'error');
                return;
            }

            // Preview gambar
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = box.querySelector('.preview');
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                preview.classList.remove('d-none');
                box.querySelector('.empty-state').classList.add('d-none');
                box.querySelector('.controls').classList.remove('d-none');
                box.querySelector('.main-choice').classList.remove('d-none');
                box.classList.add('has-image');

                // Simpan file ke queue
                queuedFiles[idx] = file;
                updateSaveButton();
                ensureBoxes();
            };
            reader.readAsDataURL(file);
        });

        // Remove button
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('Hapus gambar ini dari antrian?')) {
                // Uncheck radio jika ini yang dipilih sebagai utama
                const radio = box.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    radio.checked = false;
                }

                delete queuedFiles[idx];
                box.remove();
                updateSaveButton();
                ensureBoxes();
            }
        });

        return box;
    }

    // Fungsi untuk memastikan ada empty box
    function ensureBoxes() {
        const totalBoxes = uploadGrid.querySelectorAll('.upload-box').length;
        const filledBoxes = uploadGrid.querySelectorAll('.upload-box.has-image').length;
        const emptyBoxes = totalBoxes - filledBoxes;

        // Jika tidak ada empty box dan masih bisa tambah, tambahkan empty box
        if (totalBoxes < maxBoxes && emptyBoxes === 0 ) {
            uploadGrid.appendChild(makeEmptyBox(++queueIndex));
        }

        // Jika grid kosong, tambahkan satu empty box
        if (totalBoxes === 0) {
            uploadGrid.appendChild(makeEmptyBox(++queueIndex));
        }
    }

    // Update tombol simpan
    function updateSaveButton() {
        const hasFiles = Object.keys(queuedFiles).length > 0;
        saveBtn.disabled = !hasFiles;
        if (hasFiles) {
            saveBtn.innerHTML = `<i class="fas fa-save me-1"></i> Simpan (${Object.keys(queuedFiles).length})`;
        } else {
            saveBtn.innerHTML = `<i class="fas fa-save me-1"></i> Simpan`;
        }
    }

    // Tampilkan status
    function showStatus(message, type = 'success') {
        uploadStatus.className = `upload-status ${type}`;
        uploadStatus.textContent = message;
        uploadStatus.style.display = 'block';

        setTimeout(() => {
            uploadStatus.style.display = 'none';
        }, 3000);
    }

    // Save button handler
    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const files = Object.values(queuedFiles);

        if (files.length === 0) {
            showStatus('Tidak ada gambar untuk disimpan', 'error');
            return;
        }

        const fd = new FormData();
        Object.keys(queuedFiles).forEach(idx => {
            fd.append(`images[${idx}]`, queuedFiles[idx]);
        });

        const mainChoice = document.querySelector('input[name="main_image_choice"]:checked');
        if (mainChoice) {
            fd.append('main_image', mainChoice.value);
        }

        // Disable button dan show loading
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        fetch(`{{ route('admin.products.photos.uploadStore', $product->id) }}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf
            },
            body: fd,
            redirect: 'follow'
        })
        .then(response => {
            // Jika response redirect (status 302/301), berarti sukses
            if (response.redirected || response.status === 302 || response.status === 301) {
                showStatus('Gambar berhasil diunggah!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                return;
            }

            // Jika response tidak OK, coba parse error
            if (!response.ok) {
                return response.text().then(text => {
                    let errorMsg = 'Gagal menyimpan gambar';
                    try {
                        const json = JSON.parse(text);
                        errorMsg = json.message || json.error || errorMsg;
                    } catch (e) {
                        // Jika bukan JSON, gunakan text sebagai error
                        if (text) errorMsg = text;
                    }
                    throw new Error(errorMsg);
                });
            }

            // Jika OK, reload halaman
            showStatus('Gambar berhasil diunggah!', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            console.error('Error:', error);
            const message = error.message || 'Gagal menyimpan gambar';
            showStatus(message, 'error');
            saveBtn.disabled = false;
            updateSaveButton();
        });
    });

    // Handle existing images actions
    // const currentImagesContainer = document.getElementById('current-images');

    // currentImagesContainer.addEventListener('click', function(e) {
    //     const button = e.target.closest('button');
    //     if (!button) return;

    //     const imageId = button.getAttribute('data-image-id');
    //     if (!imageId) return;

    //     // Remove image
    //     if (button.classList.contains('remove-image')) {
    //         if (!confirm('Yakin ingin menghapus gambar ini?')) return;

    //         button.disabled = true;
    //         button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    //         fetch(`/admin/products/${productId}/photos/${imageId}/delete`, {
    //             method: 'POST',
    //             headers: {
    //                 'X-Requested-With': 'XMLHttpRequest',
    //                 'X-CSRF-TOKEN': csrf
    //             }
    //         })
    //         .then(r => r.json())
    //         .then(data => {
    //             if (data.success) {
    //                 const card = button.closest('[data-image-id]');
    //                 if (card) {
    //                     card.style.transition = 'opacity 0.3s';
    //                     card.style.opacity = '0';
    //                     setTimeout(() => card.remove(), 300);
    //                 }
    //             } else {
    //                 alert('Gagal menghapus gambar');
    //                 button.disabled = false;
    //                 button.innerHTML = '<i class="fas fa-trash"></i>';
    //             }
    //         })
    //         .catch(err => {
    //             console.error(err);
    //             alert('Gagal menghapus gambar');
    //             button.disabled = false;
    //             button.innerHTML = '<i class="fas fa-trash"></i>';
    //         });
    //     }

    //     // Set main image
    //     if (button.classList.contains('btn-set-main')) {
    //         button.disabled = true;
    //         button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    //         fetch(`/admin/products/${productId}/photos/${imageId}/set-main`, {
    //             method: 'POST',
    //             headers: {
    //                 'X-Requested-With': 'XMLHttpRequest',
    //                 'X-CSRF-TOKEN': csrf
    //             }
    //         })
    //         .then(r => r.json())
    //         .then(data => {
    //             if (data.success) {
    //                 // Update semua badge
    //                 currentImagesContainer.querySelectorAll('.badge').forEach(badge => {
    //                     badge.className = 'badge bg-secondary';
    //                     badge.textContent = 'Tambahan';
    //                 });

    //                 // Set badge utama pada gambar yang dipilih
    //                 const card = button.closest('[data-image-id]');
    //                 const badgeWrap = card.querySelector('.badge');
    //                 if (badgeWrap) {
    //                     badgeWrap.className = 'badge bg-primary';
    //                     badgeWrap.textContent = 'Utama';
    //                 }
    //             } else {
    //                 alert('Gagal mengatur gambar utama');
    //             }
    //             button.disabled = false;
    //             button.innerHTML = '<i class="fas fa-star"></i>';
    //         })
    //         .catch(err => {
    //             console.error(err);
    //             alert('Gagal mengatur gambar utama');
    //             button.disabled = false;
    //             button.innerHTML = '<i class="fas fa-star"></i>';
    //         });
    //     }
    // });

    // Initialize
    ensureBoxes();
});
</script>
@endpush
