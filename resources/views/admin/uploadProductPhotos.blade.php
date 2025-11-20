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

<div class="card shadow-sm">
    <div class="card-body">
        <h5 class="card-title">Upload Gambar untuk: {{ $product->nama_produk }}</h5>
        <p class="text-muted">SKU: {{ $product->kode_sku }}</p>

        <div class="mb-3">
            <label class="form-label">Unggah Gambar (maks 10). Pilih satu per kotak lalu klik Upload.</label>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <small class="text-muted">Klik kotak untuk memilih gambar. Setelah memilih, gambar akan ditampilkan di preview tetapi belum disimpan ke database sampai Anda menekan tombol Simpan.</small>
                </div>
                <div>
                    <button id="save-uploads" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </div>

            <div id="upload-grid" class="d-flex flex-wrap gap-3">
    </div>

<hr class="mt-4">
<h5 class="card-title">Gambar Saat Ini</h5>
<div id="current-images" class="d-flex flex-wrap gap-3">
    {{-- Loop untuk menampilkan gambar yang sudah ada harusnya ada di sini --}}
    @forelse ($product->photos as $photo)
    <div class="card" style="width: 160px;" data-image-id="{{ $photo->id }}">
        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" style="height: 160px; object-fit: cover;">
        <div class="card-body p-1 d-flex justify-content-between align-items-center">
            <div>
                @if ($photo->is_main)
                <span class="badge bg-primary">Utama</span>
                @endif
            </div>
            <div>
                <button class="btn btn-sm btn-info text-white btn-set-main" data-image-id="{{ $photo->id }}" title="Set Utama"><i class="fas fa-star"></i></button>
                <button class="btn btn-sm btn-danger remove-image" data-image-id="{{ $photo->id }}" title="Hapus"><i class="fas fa-times"></i></button>
            </div>
        </div>
    </div>
    @empty
    <p class="text-muted">Belum ada gambar yang diunggah.</p>
    @endforelse
</div>


@endsection

{{-- Pindahkan semua kode JS ke @push('scripts') yang biasanya ada di akhir body --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const maxBoxes = 10;
        const uploadGrid = document.getElementById('upload-grid');
        const saveBtn = document.getElementById('save-uploads');
        const productId = '{{ $product->id }}';
        const csrf = '{{ csrf_token() }}';

        let queueIndex = 0; // incremental index for new files
        const queuedFiles = {}; // map index -> File

        // Fungsi makeEmptyBox() harus dipindahkan ke sini
        function makeEmptyBox(idx) {
            // ... (Isi fungsi makeEmptyBox yang sama)
            const box = document.createElement('div');
            box.className = 'upload-box position-relative d-flex align-items-center justify-content-center';
            box.style.width = '160px';
            box.style.height = '160px';
            box.style.background = '#f8f9fc';
            box.style.border = '1px dashed #ced4da';
            box.style.borderRadius = '6px';
            box.style.cursor = 'pointer';
            box.style.overflow = 'hidden';

            box.innerHTML = `
                <input type="file" accept="image/*" class="d-none file-input">
                <div class="empty-state text-center text-muted w-100">
                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                    <div>Upload</div>
                </div>
                <div class="preview d-none" style="width:100%;height:100%;"></div>
                <div class="main-choice position-absolute start-0 bottom-0 m-1 d-none">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input main-radio-new" type="radio" name="main_image_choice" value="new_${idx}" id="newMain${idx}">
                    </div>
                </div>
                <div class="controls position-absolute top-0 end-0 m-1 d-none">
                    <button class="btn btn-sm btn-danger btn-delete" title="Hapus"><i class="fas fa-times"></i></button>
                </div>
            `;

            const input = box.querySelector('.file-input');
            box.addEventListener('click', function(e) {
                if (e.target.closest('.controls')) return;
                input.click();
            });

            input.addEventListener('change', function() {
                const f = this.files[0];
                if (!f) return;
                if (!f.type.startsWith('image/')) { alert('File harus gambar'); return; }
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const preview = box.querySelector('.preview');
                    preview.innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;" />`;
                    preview.classList.remove('d-none');
                    box.querySelector('.empty-state').classList.add('d-none');
                    box.querySelector('.controls').classList.remove('d-none');
                    box.querySelector('.main-choice').classList.remove('d-none');
                    // store in queue
                    queuedFiles[idx] = f;
                    // after adding a file, ensure there's another empty box if limit not reached
                    ensureBoxes();
                };
                reader.readAsDataURL(f);
            });

            // delete handler for queued item
            box.querySelector('.controls').addEventListener('click', function(e) {
                e.stopPropagation();
                const del = box.querySelector('.btn-delete');
                del.addEventListener('click', function() {
                    // Cek apakah yang dihapus adalah radio utama
                    if(box.querySelector('.main-radio-new').checked) {
                         // uncheck radio jika dihapus
                         document.querySelector('input[name="main_image_choice"]').checked = false;
                    }
                    delete queuedFiles[idx];
                    box.remove();
                    ensureBoxes();
                });
            });

            return box;
        }

        // Fungsi ensureBoxes() dipindahkan ke sini
        function ensureBoxes() {
            const totalBoxes = uploadGrid.querySelectorAll('.upload-box').length;
            // if no empty box and we can add more, append
            if (totalBoxes < maxBoxes && uploadGrid.querySelectorAll('.upload-box .preview.d-none').length === 0) {
                uploadGrid.appendChild(makeEmptyBox(++queueIndex));
            }
            // always ensure at least one empty box if grid is empty
            if (totalBoxes === 0) {
                uploadGrid.appendChild(makeEmptyBox(++queueIndex));
            }
        }

        // initial box
        uploadGrid.appendChild(makeEmptyBox(++queueIndex));

        // Save button handler: send queued files and main selection to server
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const fd = new FormData();
            const files = Object.values(queuedFiles);
            if (files.length === 0) {
                alert('Tidak ada gambar baru untuk disimpan');
                return;
            }
            // Append files with their original queue index to match main image choice if needed
            Object.keys(queuedFiles).forEach(idx => {
                 fd.append(`images[${idx}]`, queuedFiles[idx]);
            });

            const mainChoice = document.querySelector('input[name="main_image_choice"]:checked');
            if (mainChoice) {
                fd.append('main_image', mainChoice.value);
            }

            saveBtn.disabled = true;
            saveBtn.textContent = 'Menyimpan...';

            fetch(`{{ route('admin.products.photos.uploadStore', $product->id) }}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: fd,
            }).then(r => {
                if (r.redirected) {
                    window.location.href = r.url;
                    return null;
                }
                return r.text();
            }).then(body => {
                // on success the server redirects back to list; if not redirected, reload
                if (body !== null) {
                    location.reload();
                }
            }).catch(err => {
                console.error(err);
                alert('Gagal menyimpan gambar');
            }).finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Simpan';
            });
        });

        // existing image delete (AJAX) and set-main (AJAX) handled here
        document.getElementById('current-images').addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;

            const id = target.getAttribute('data-image-id');
            if (!id) return;

            if (target.classList.contains('remove-image')) {
                if (!confirm('Hapus gambar ini?')) return;
                fetch(`{{ url('/admin/products') }}/${productId}/photos/${id}/delete`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        const el = document.querySelector('#current-images [data-image-id="' + id + '"]');
                        if (el) el.remove();
                    } else {
                        alert('Gagal menghapus');
                    }
                }).catch(err => { console.error(err); alert('Gagal menghapus'); });
            }

            if (target.classList.contains('btn-set-main')) {
                fetch(`{{ url('/admin/products') }}/${productId}/photos/${id}/set-main`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        document.querySelectorAll('#current-images .badge').forEach(el => el.remove());
                        const parent = target.closest('[data-image-id]');
                        const badgeWrap = parent.querySelector('.card-body div');
                        if (badgeWrap) badgeWrap.innerHTML = '<span class="badge bg-primary">Utama</span>';
                    } else {
                        alert('Gagal set utama');
                    }
                }).catch(err => { console.error(err); alert('Gagal set utama'); });
            }
        });
    });
</script>
@endpush
