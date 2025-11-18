<?php

namespace App\Http\Controllers;

use App\Models\CatalogSettings;
use App\Models\CatalogBanners;
use App\Models\CatalogPartnerLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSettingsController extends Controller
{
    public function edit()
    {
        $cat_setting = CatalogSettings::first();
        $cat_banners = CatalogBanners::all();
        $cat_partner = CatalogPartnerLogo::all();
        return view('admin.catalog-settings', [
            'cat_setting' => $cat_setting,
            'cat_banners' => $cat_banners,
            'cat_partner' => $cat_partner,
        ]);
    }
    public function update(Request $request)
    {
        $cat_setting = CatalogSettings::first();
        $cat_partner = CatalogPartnerLogo::all();
        $cat_banners = CatalogBanners::all();

        // Validasi
        $request->validate([
            'site_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'brand_logos' => 'nullable|image|max:2048',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_tiktok' => 'nullable|url',
            'social_youtube' => 'nullable|url',
            'social_tokopedia' => 'nullable|url',
            'social_shopee' => 'nullable|url',
            'contact_phone' => 'nullable|string|max:20',
            'description_text' => 'nullable|string',
        ]);

        // Update text fields
        $cat_setting->update([
            'nama_website' => $request->site_name,
            'nomor_telfon' => $request->contact_phone,
            'description' => $request->description_text,
            'facebook_link' => $request->social_facebook,
            'instagram_link' => $request->social_instagram,
            'tiktok_link' => $request->social_tiktok,
            'youtube_link' => $request->social_youtube,
            'tokopedia_link' => $request->social_tokopedia,
            'shopee_link' => $request->social_shopee,
        ]);
        // hapus logo partner yang ditandai user
        if ($request->filled('deleted_partners')) {
            $ids = json_decode($request->deleted_partners, true);

            // ambil record dulu
            $partners = CatalogPartnerLogo::whereIn('id', $ids)->get();

            foreach ($partners as $partner) {
                // hapus file jika ada di storage/photos
                if ($partner->logo_path && Str::startsWith($partner->logo_path, 'photos/')) {
                    Storage::disk('public')->delete($partner->logo_path);
                }
            }

            // hapus record dari database
            CatalogPartnerLogo::whereIn('id', $ids)->delete();
        }

        // hapus banner yang ditandai user
        if ($request->filled('deleted_banners')) {
            $ids = json_decode($request->deleted_banners, true);

            $banners = CatalogBanners::whereIn('id', $ids)->get();

            foreach ($banners as $banner) {
                if ($banner->banner_path && Str::startsWith($banner->banner_path, 'photos/')) {
                    Storage::disk('public')->delete($banner->banner_path);
                }
            }

            CatalogBanners::whereIn('id', $ids)->delete();
        }

        $path = null;
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('photos', 'public');
            CatalogBanners::create([
                'banner_path' => $path,
                'catalog_setting_id' => 1,
            ]);
        }
        if ($request->hasFile('brand_logos')) {
            $path = $request->file('brand_logos')->store('photos', 'public');
            CatalogPartnerLogo::create([
                'logo_path' => $path,
                'catalog_setting_id' => 1,
            ]);
        }
        if ($request->hasFile('photo_logo')) {

            // jangan dinyalain dulu sebelum siap  !!
            // if ($cat_setting->logo_path && Storage::disk('public')->exists($cat_setting->logo_path)) {
            //     Storage::disk('public')->delete($cat_setting->logo_path);
            // }
            $path = $request->file('photo_logo')->store('photos', 'public');

            // Update record dengan id 1
            CatalogSettings::where('id', 1)->update([
                'logo_path' => $path,
            ]);
        }

        $cat_setting->save();
        return redirect()->route('admin.catalog-settings.index')->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }

}

