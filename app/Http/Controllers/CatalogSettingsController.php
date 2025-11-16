<?php

namespace App\Http\Controllers;

use App\Models\CatalogSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogSettingsController extends Controller
{
    public function edit()
    {
        $setting = CatalogSetting::first();

        if (!$setting) {
            $setting = CatalogSetting::create([]);
        }

        return view('admin.catalog-settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = CatalogSetting::first();

        if (!$setting) {
            $setting = CatalogSetting::create([]);
        }

        $validated = $request->validate([
            'site_name'         => 'nullable|string|max:100',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'banner'            => 'nullable|image|mimes:jpeg,png,jpg,svg|max:4096',
            'brand_logo_1'      => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'brand_logo_2'      => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'brand_logo_3'      => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'brand_logo_4'      => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'social_facebook'   => 'nullable|string|max:255',
            'social_instagram'  => 'nullable|string|max:255',
            'social_whatsapp'   => 'nullable|string|max:255',
            'social_tiktok'     => 'nullable|string|max:255',
            'social_youtube'    => 'nullable|string|max:255',
            'contact_phone'     => 'nullable|string|max:50',
            'contact_email'     => 'nullable|email|max:100',
            'address_text'      => 'nullable|string',
        ]);

        $setting->site_name = $validated['site_name'] ?? $setting->site_name;
        $setting->social_facebook = $validated['social_facebook'] ?? null;
        $setting->social_instagram = $validated['social_instagram'] ?? null;
        $setting->social_whatsapp = $validated['social_whatsapp'] ?? null;
        $setting->social_tiktok = $validated['social_tiktok'] ?? null;
        $setting->social_youtube = $validated['social_youtube'] ?? null;
        $setting->contact_phone = $validated['contact_phone'] ?? null;
        $setting->contact_email = $validated['contact_email'] ?? null;
        $setting->address_text = $validated['address_text'] ?? null;

        $uploadImage = function (Request $request, CatalogSetting $setting, string $field, string $column) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                if ($setting->$column) {
                    Storage::disk('public')->delete($setting->$column);
                }

                $path = $file->store('catalog', 'public');
                $setting->$column = $path;
            }
        };

        $uploadImage($request, $setting, 'logo', 'logo_path');
        $uploadImage($request, $setting, 'banner', 'banner_path');
        $uploadImage($request, $setting, 'brand_logo_1', 'brand_logo_1_path');
        $uploadImage($request, $setting, 'brand_logo_2', 'brand_logo_2_path');
        $uploadImage($request, $setting, 'brand_logo_3', 'brand_logo_3_path');
        $uploadImage($request, $setting, 'brand_logo_4', 'brand_logo_4_path');

        $setting->save();

        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil disimpan.');
    }
}

