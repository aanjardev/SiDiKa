<?php

namespace App\Http\Controllers;

use App\Helpers\ImageUpload;
use App\Http\Requests\DinoyoWayUpdateRequest;
use App\Models\CatalogSettings;
use App\Services\DinoyoWayContent;
use Illuminate\Support\Arr;

class DinoyoWayController extends Controller
{
    public function edit(DinoyoWayContent $page)
    {
        $content = $page->get();

        return view('admin.dinoyo-way', [
            'content' => $page->editable($content),
            'heroImageUrl' => $page->heroImageUrl($content),
            'imageSlots' => $page->imageSlots($content),
            'sections' => array_intersect_key(DinoyoWayContent::SECTIONS, $page->editable($content)),
            'labels' => DinoyoWayContent::LABELS,
        ]);
    }

    public function update(DinoyoWayUpdateRequest $request, DinoyoWayContent $page)
    {
        $content = $page->get();
        foreach (Arr::dot($request->validated('content')) as $path => $value) {
            Arr::set($content, $path, str_ends_with($path, '.percentage') ? (int) $value : $value);
        }
        $imageFields = ['hero' => 'hero.image'] + DinoyoWayContent::IMAGE_FIELDS;
        foreach ($imageFields as $key => $path) {
            $fileField = $key === 'hero' ? 'hero_image' : 'page_images.'.$key;
            $resetField = $key === 'hero' ? 'reset_hero_image' : 'reset_page_images.'.$key;
            if ($request->boolean($resetField)) {
                Arr::set($content, $path, config('dinoyo-way.'.$path));
            } elseif ($request->hasFile($fileField)) {
                try {
                    Arr::set($content, $path, ImageUpload::upload($request->file($fileField), 'catalog/dinoyo-way', 'public')['path']);
                } catch (\Throwable $exception) {
                    report($exception);
                    return back()->withInput($request->except('hero_image', 'page_images'))
                        ->withErrors([$fileField => 'Foto gagal disimpan. Pilih ulang foto dan coba lagi. Teks yang Anda isi tetap dipertahankan.']);
                }
            }
        }

        $settings = CatalogSettings::first() ?? new CatalogSettings(['nama_website' => 'Dinoyo Kamera']);
        $settings->dinoyo_way_content = $content;
        $settings->save();

        return redirect()->route('admin.dinoyo-way.edit')
            ->with('success', 'Konten Dinoyo Kamera Way berhasil diperbarui.');
    }
}
