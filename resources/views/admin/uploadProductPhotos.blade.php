@extends('layouts.admin')

@section('title', 'Upload Foto Produk')

@push('page-actions')
    <a href="{{ route('admin.products.photos') }}" class="btn btn-light border px-3 fw-medium text-secondary d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
@endpush

@section('content')

<div class="row">
    <div class="col-12">
        
        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4 border-0 shadow-sm">
                <h5 class="alert-heading small fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Ada Kesalahan!</h5>
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            
            {{-- Card Header (Disatukan dengan Info Produk agar rapi) --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-images me-2 text-primary"></i>
                            Upload Foto Produk
                        </h5>
                        <p class="text-muted small mt-1 mb-0">
                            Produk: <strong class="text-dark">{{ $product->nama_produk }}</strong> &bull; SKU: <span class="badge bg-light text-dark border">{{ $product->kode_sku }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                
                {{-- Toolbar / Instruksi --}}
                <div class="bg-light p-3 rounded mb-4 border-start border-4 border-primary d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="small text-muted">
                        <i class="fa-solid fa-circle-info me-1 text-primary"></i>
                        Maksimal <strong>10 gambar</strong> (Max 5MB/file). Klik kotak di bawah untuk memilih gambar.
                    </div>
                    
                    {{-- Tombol Simpan --}}
                    <button id="save-uploads" class="btn btn-primary px-4 fw-medium d-flex align-items-center gap-2" disabled>
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

                <form id="upload-photos-form" action="{{ route('admin.products.photos.uploadStore', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Hidden file input yang akan diisi via JavaScript --}}
                    <input type="file" name="images[]" id="hidden-images-input" class="d-none" multiple>
                    <input type="hidden" name="main_image" id="hidden-main-image">

                    {{-- Area Upload Grid --}}
                    <div id="upload-grid" class="d-flex flex-wrap gap-3">
                        {{-- Upload boxes akan ditambahkan via JavaScript --}}
                    </div>

                    <div id="upload-status" class="mt-3"></div>
                </form>

                {{-- Gambar Saat Ini (Komentar Asli) --}}
                {{-- 
                <hr class="my-5 opacity-25">
                <h6 class="fw-bold text-dark mb-3">Gambar Saat Ini</h6>
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
                    <div class="w-100 text-center py-4 bg-light rounded border border-dashed">
                        <i class="fas fa-image fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">Belum ada gambar yang diunggah.</p>
                    </div>
                    @endforelse
                </div> 
                --}}

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/upload-photo.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const maxBoxes = 10;
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const uploadGrid = document.getElementById('upload-grid');
    const saveBtn = document.getElementById('save-uploads');
    const uploadStatus = document.getElementById('upload-status');
    const form = document.getElementById('upload-photos-form');
    const hiddenImagesInput = document.getElementById('hidden-images-input');
    const hiddenMainImageInput = document.getElementById('hidden-main-image');
    const productId = '{{ $product->id }}';
    const csrf = '{{ csrf_token() }}';

    let queueIndex = 0;
    const queuedFiles = {}; // map index -> File

    // Fungsi untuk membuat upload box
    function makeEmptyBox(idx) {
        const box = document.createElement('div');
        box.className = 'upload-box';
        box.dataset.boxIndex = idx;

        // HTML structure di dalam box (tidak diubah logicnya)
        box.innerHTML = `
            <input type="file" accept="image/*" class="d-none file-input" data-index="${idx}">
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
            <div class="main-choice d-none">
                <label class="form-check-label">
                    <input type="radio" name="main_image_choice" value="new_${idx}" class="form-check-input mt-0">
                    <span>Utama</span>
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
            // if (confirm('Hapus gambar ini dari antrian?')) { // Optional: remove confirm for smoother UX
                // Uncheck radio jika ini yang dipilih sebagai utama
                const radio = box.querySelector('input[type="radio"]');
                if (radio && radio.checked) {
                    radio.checked = false;
                }

                delete queuedFiles[idx];
                box.remove();
                updateSaveButton();
                ensureBoxes();
            // }
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
            saveBtn.innerHTML = `<i class="fas fa-save"></i> Simpan (${Object.keys(queuedFiles).length})`;
        } else {
            saveBtn.innerHTML = `<i class="fas fa-save"></i> Simpan Perubahan`;
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

        // Siapkan FileList untuk input file hidden menggunakan DataTransfer
        const dt = new DataTransfer();
        Object.values(queuedFiles).forEach(file => {
            dt.items.add(file);
        });
        hiddenImagesInput.files = dt.files;

        // Set main_image jika dipilih
        const mainChoice = document.querySelector('input[name="main_image_choice"]:checked');
        if (mainChoice) {
            hiddenMainImageInput.value = mainChoice.value;
        } else {
            hiddenMainImageInput.value = '';
        }

        // Disable button dan show loading
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        // Submit form biasa agar redirect & flash alert bekerja
        form.submit();
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