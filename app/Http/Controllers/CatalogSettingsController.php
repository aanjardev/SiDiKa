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
        return view('admin.catalog-settings', [
            'cat_setting' => CatalogSettings::first(),
            'cat_banners' => CatalogBanners::all(),
            'cat_partner' => CatalogPartnerLogo::all(),
        ]);
    }

    public function update(Request $request)
    {
        $cat_setting = CatalogSettings::first();

        $request->validate([
            'site_name'         => 'required|string|max:255',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'brand_logos'       => 'nullable|image|max:2048',
            'banner'            => 'nullable|image|max:4096',
            'social_facebook'   => 'nullable|url',
            'social_instagram'  => 'nullable|url',
            'social_tiktok'     => 'nullable|url',
            'social_youtube'    => 'nullable|url',
            'social_tokopedia'  => 'nullable|url',
            'social_shopee'     => 'nullable|url',
            'contact_phone'     => 'nullable|string|max:20',
            'description_text'  => 'nullable|string',
        ]);

        $cat_setting->update([
            'nama_website'   => $request->site_name,
            'nomor_telfon'   => $request->contact_phone,
            'description'     => $request->description_text,
            'facebook_link'   => $request->social_facebook,
            'instagram_link'  => $request->social_instagram,
            'tiktok_link'     => $request->social_tiktok,
            'youtube_link'    => $request->social_youtube,
            'tokopedia_link'  => $request->social_tokopedia,
            'shopee_link'     => $request->social_shopee,
        ]);

        if ($request->filled('deleted_partners')) {
            $ids = json_decode($request->deleted_partners, true);

            $partners = CatalogPartnerLogo::whereIn('id', $ids)->get();

            foreach ($partners as $partner) {

                if ($partner->logo_path && Storage::disk('r2')->exists($partner->logo_path)) {
                    Storage::disk('r2')->delete($partner->logo_path);
                }
            }

            CatalogPartnerLogo::whereIn('id', $ids)->delete();
        }

        if ($request->filled('deleted_banners')) {
            $ids = json_decode($request->deleted_banners, true);

            $banners = CatalogBanners::whereIn('id', $ids)->get();

            foreach ($banners as $banner) {

                if ($banner->banner_path && Storage::disk('r2')->exists($banner->banner_path)) {
                    Storage::disk('r2')->delete($banner->banner_path);
                }
            }

            CatalogBanners::whereIn('id', $ids)->delete();
        }

        if ($request->hasFile('banner')) {

            $ext = $request->file('banner')->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $ext;

            $path = Storage::disk('r2')->putFileAs(
                'catalog/banners',
                $request->file('banner'),
                $filename,
                ['visibility' => 'public']
            );

            CatalogBanners::create([
                'banner_path' => $path,
                'catalog_setting_id' => 1,
            ]);
        }

        if ($request->hasFile('brand_logos')) {

            $ext = $request->file('brand_logos')->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $ext;

            $path = Storage::disk('r2')->putFileAs(
                'catalog/partners',
                $request->file('brand_logos'),
                $filename,
                ['visibility' => 'public']
            );

            CatalogPartnerLogo::create([
                'logo_path' => $path,
                'catalog_setting_id' => 1,
            ]);
        }

        if ($request->hasFile('photo_logo')) {

            // hapus file lama
            if ($cat_setting->logo_path && Storage::disk('r2')->exists($cat_setting->logo_path)) {
                Storage::disk('r2')->delete($cat_setting->logo_path);
            }

            $ext = $request->file('photo_logo')->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $ext;

            $path = Storage::disk('r2')->putFileAs(
                'catalog/logo',
                $request->file('photo_logo'),
                $filename,
                ['visibility' => 'public']
            );

            $cat_setting->update([
                'logo_path' => $path,
            ]);
        }


        return redirect()
            ->route('admin.catalog-settings.index')
            ->with('success', 'Pengaturan katalog berhasil diperbarui.');
    }
}

