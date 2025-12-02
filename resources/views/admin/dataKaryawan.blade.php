@extends('layouts.admin')

@section('title', 'Data Karyawan')

@push('page-actions')
<a href="{{ route('admin.employees.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Karyawan</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian (Resolved: Visual Design-Wafa + Logic Main) --}}
<form method="GET" action="{{ route('admin.employees.index') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2">
                <span class="text-muted ms-2 me-3">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari karyawan berdasarkan nama..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;"
                       autofocus>
            </div>

            {{-- Bagian Kanan: Dropdown Filter --}}
            <div class="d-flex align-items-center gap-2 pe-2">

                {{-- Dropdown Jabatan --}}
                <select name="jabatan"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_jabatan ?? 'all') == 'all' ? 'selected' : '' }}>Semua Jabatan</option>
                    <option value="Manager" {{ ($selected_jabatan ?? 'all') == 'Manager' ? 'selected' : '' }}>Manager</option>
                    <option value="Staff Operasional" {{ ($selected_jabatan ?? 'all') == 'Staff Operasional' ? 'selected' : '' }}>Staff Operasional</option>
                </select>

                {{-- Dropdown Status --}}
                <select name="status"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_status ?? 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="aktif" {{ ($selected_status ?? 'all') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="non-aktif" {{ ($selected_status ?? 'all') == 'non-aktif' ? 'selected' : '' }}>Non Aktif</option>
                </select>

                {{-- Dropdown Sort --}}
                <select name="sort_by" 
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium" 
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ ($sort_by ?? 'updated_at') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                </select>
                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 30%;">Nama Karyawan</th>
                        <th>Nomor Telepon</th>
                        <th>Jabatan</th>
                        <th class="text-center">Lama Bekerja</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.employees.show', $employee->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($employees->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                {{-- Avatar Placeholder --}}
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary"
                                        style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $employee->nama_lengkap }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="fw-medium text-secondary text-nowrap">
                            {{ $employee->nomor_telepon }}
                        </td>

                        <td class="text-dark">
                            {{ $employee->jabatan }}
                        </td>

                        <td class="text-center">
                            @php
                                $lamaKerja = null;

                                $start = $employee->tanggal_masuk ?? null;
                                $end = null;

                                if ($start) {
                                    if ($employee->status === 'aktif') {
                                        $end = now();
                                    } else {
                                        $end = $employee->tanggal_keluar ?? now();
                                    }

                                    $diff = $start->diff($end);

                                    $parts = [];
                                    if ($diff->y) {
                                        $parts[] = $diff->y . ' th';
                                    }
                                    if ($diff->m) {
                                        $parts[] = $diff->m . ' bln';
                                    }
                                    if (!$diff->y && !$diff->m) {
                                        $parts[] = $diff->d . ' hr';
                                    }

                                    $lamaKerja = implode(' ', $parts);
                                }
                            @endphp

                            @php
                                $tooltip = null;
                                if ($employee->tanggal_masuk) {
                                    $tooltip = 'Mulai: ' . $employee->tanggal_masuk->format('d M Y');
                                    if ($employee->tanggal_keluar) {
                                        $tooltip .= ' · Selesai: ' . $employee->tanggal_keluar->format('d M Y');
                                    }
                                }
                            @endphp

                            <span @if($tooltip) title="{{ $tooltip }}" @endif>
                                {{ $lamaKerja ?? '-' }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if ($employee->status === 'aktif')
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
                                <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit"
                                    onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-users-slash fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data karyawan</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($employees->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $employees->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
