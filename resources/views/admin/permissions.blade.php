@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')

@push('page-actions')
<a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah User</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
    <div class="card-body p-2 d-flex align-items-center flex-wrap">
        {{-- Bagian Kiri: Input Search --}}
        <div class="d-flex align-items-center flex-grow-1 ps-2">
            <span class="text-muted ms-2 me-3">
                <i class="fa-solid fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-0 shadow-none bg-transparent"
                placeholder="Cari user berdasarkan nama..."
                style="font-size: 0.95rem;"
                autofocus>
        </div>

        {{-- Bagian Kanan: Dropdown --}}
        <div class="d-flex align-items-center gap-2 pe-2">
            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Semua Jabatan</option>
            </select>

            <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" style="cursor: pointer;">
                <option selected>Status</option>
                <option value="aktif">Aktif</option>
                <option value="non_aktif">Non Aktif</option>
            </select>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama Lengkap</th>
                        <th>Email</th>
                        <th>Jenis Akses</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($user_data as $k)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.permissions.edit', $k->id) }}">
                        <td class="text-center text-muted fw-bold">{{ $k->id }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                {{-- Avatar Placeholder --}}
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary"
                                        style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $k->name }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="fw-medium text-secondary">
                            {{ $k->email }}
                        </td>

                        <td>
                            <span class="fw-medium text-dark">{{ $k->role }}</span>
                        </td>

                        <td class="text-center">
                            @if($k->status == 'aktif')
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size: 0.85rem;">
                                    Aktif
                                </span>
                            @else
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.85rem;">
                                    Non Aktif
                                </span>
                            @endif
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.permissions.edit', $k->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.permissions.destroy', $k->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-action btn-action-delete"
                                        title="Hapus"
                                        onclick="handleDeletePermission(this)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if (method_exists($user_data, 'hasPages') && $user_data->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $user_data->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function handleDeletePermission(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Yakin mau hapus data ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Yakin mau hapus data ini?')) {
                button.form.submit();
            }
        }
    }
    
    // Export ke window
    window.handleDeletePermission = handleDeletePermission;
</script>
@endpush
