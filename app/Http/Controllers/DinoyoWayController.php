<?php

namespace App\Http\Controllers;

use App\Http\Requests\DinoyoWayUpdateRequest;
use App\Models\CatalogSettings;
use App\Services\DinoyoWayContent;

class DinoyoWayController extends Controller
{
    public function edit(DinoyoWayContent $page)
    {
        $content = $page->get();

        return view('admin.dinoyo-way', [
            'content' => $page->editable($content),
            'sections' => DinoyoWayContent::SECTIONS,
        ]);
    }

    public function update(DinoyoWayUpdateRequest $request, DinoyoWayContent $page)
    {
        $settings = CatalogSettings::first() ?? new CatalogSettings(['nama_website' => 'Dinoyo Kamera']);
        $settings->dinoyo_way_content = $request->validated('content');
        $settings->save();

        return redirect()->route('admin.dinoyo-way.edit')
            ->with('success', 'Konten Dinoyo Kamera Way berhasil diperbarui.');
    }
}
