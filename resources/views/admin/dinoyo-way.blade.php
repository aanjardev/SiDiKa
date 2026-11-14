@extends('layouts.admin')

@section('title', 'Setting Dinoyo Kamera Way')

@push('page-actions')
    <a href="{{ route('dinoyo-way') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener"><i class="fas fa-arrow-up-right-from-square me-1" aria-hidden="true"></i>Lihat Halaman</a>
@endpush

@push('styles')
<style>
    .way-editor { max-width: 1100px; margin: 0 auto; }
    .way-editor-intro { color: #6c757d; font-size: .9rem; margin-bottom: 1.5rem; }
    .way-editor-section { border: 1px solid #e9ecef; border-radius: 10px; background: #fff; margin-bottom: 1rem; }
    .way-editor-section > summary { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1.25rem 1.5rem; cursor: pointer; list-style: none; font-weight: 650; color: #2f353f; }
    .way-editor-section > summary::-webkit-details-marker { display: none; }
    .way-editor-section > summary:focus-visible { outline: 3px solid #4e6bff; outline-offset: 2px; }
    .way-editor-section[open] > summary { border-bottom: 1px solid #e9ecef; }
    .way-editor-section[open] > summary > i { transform: rotate(180deg); }
    .way-editor-body { padding: 1.5rem; }
    .way-editor .form-label { font-size: .85rem; font-weight: 600; color: #3d4550; }
    .way-editor-item { border-left: 2px solid #e9ecef; padding-left: 1.25rem; margin: 1.25rem 0 1.75rem; }
    .way-editor-item-title { color: #4e6bff; font-size: .85rem; font-weight: 700; margin-bottom: 1rem; }
    .way-editor-save { position: sticky; bottom: 0; z-index: 800; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.5rem; background: #fff; border: 1px solid #e9ecef; border-radius: 10px; box-shadow: 0 -4px 20px rgba(0,0,0,.04); }
    .way-editor-save p { margin: 0; font-size: .8rem; color: #6c757d; }
    @media (max-width: 575px) { .way-editor-section > summary, .way-editor-body { padding: 1rem; } .way-editor-item { padding-left: .75rem; } .way-editor-save { flex-direction: column; align-items: stretch; padding: 1rem; } }
</style>
@endpush

@section('content')
<div class="way-editor">
    <p class="way-editor-intro">Edit copy page publik. Layout, animasi, gambar, dan link resmi tetap dikunci agar konsisten dengan Dinoyo Kamera Way.</p>
    @if(session('success')) <div class="alert alert-success" role="alert">{{ session('success') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert"><strong>Perubahan belum disimpan.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form id="dinoyoWayForm" action="{{ route('admin.dinoyo-way.update') }}" method="POST">
        @csrf
        <details class="way-editor-section" open><summary>Identitas halaman <i class="fas fa-chevron-down" aria-hidden="true"></i></summary><div class="way-editor-body">@include('admin.partials.dinoyo_way_fields', ['fields' => \Illuminate\Support\Arr::only($content, ['title', 'navigation_label', 'description', 'navigation_aria_label']), 'prefix' => 'content'])</div></details>
        @foreach($sections as $key => $label)
            @if(array_key_exists($key, $content))
                <details class="way-editor-section" @if($errors->has('content.'.$key) || $errors->has('content.'.$key.'.*')) open @endif>
                    <summary>{{ $label }} <i class="fas fa-chevron-down" aria-hidden="true"></i></summary>
                    <div class="way-editor-body">@include('admin.partials.dinoyo_way_fields', ['fields' => $content[$key], 'prefix' => 'content.'.$key])</div>
                </details>
            @endif
        @endforeach
        <div class="way-editor-save"><p>Perubahan tersimpan langsung ke page publik.</p><button type="submit" class="btn btn-primary"><i class="fas fa-save me-2" aria-hidden="true"></i>Simpan Perubahan</button></div>
    </form>
</div>
@endsection
