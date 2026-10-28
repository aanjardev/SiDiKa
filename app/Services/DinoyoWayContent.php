<?php

namespace App\Services;

use App\Models\CatalogSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DinoyoWayContent
{
    public const SECTIONS = [
        'hero' => 'Pembuka & foto hero',
        'navigation' => 'Navigasi bagian',
        'philosophy' => '01. Siapa kami',
        'mission' => '02. Misi besar kami',
        'values' => '03. Enam pilar SIAPPPP',
        'standards' => '04. Standar kerja',
        'daily' => '05. Daily Winning System',
        'performance' => '06. Budaya kinerja',
        'career' => '07. Pertumbuhan karier',
        'evaluation' => '08. Evaluasi & bobot penilaian',
        'pledge' => '09. Janji bersama',
        'implementation' => '10. Rekomendasi implementasi',
        'closing' => 'Penutup & ajakan',
    ];

    public const IMAGE_FIELDS = [
        'philosophy' => 'philosophy.image',
        'standards' => 'standards.image',
        'pillar_0' => 'values.items.0.image',
        'pillar_1' => 'values.items.1.image',
        'pillar_2' => 'values.items.2.image',
        'pillar_3' => 'values.items.3.image',
        'pillar_4' => 'values.items.4.image',
        'pillar_5' => 'values.items.5.image',
    ];

    public const LABELS = [
        'title' => 'Judul', 'navigation_label' => 'Nama menu navbar & footer',
        'description' => 'Deskripsi', 'navigation_aria_label' => 'Deskripsi navigasi untuk pembaca layar',
        'eyebrow' => 'Label bagian', 'statement' => 'Pernyataan utama',
        'action' => 'Teks tombol', 'image_alt' => 'Teks alternatif foto', 'caption' => 'Keterangan foto',
        'label' => 'Nama tautan', 'lead' => 'Paragraf pembuka', 'paragraphs' => 'Paragraf',
        'detail' => 'Penjelasan tambahan', 'behavior_label' => 'Label contoh perilaku',
        'items' => 'Elemen', 'name' => 'Nama pilar', 'behavior' => 'Contoh perilaku',
        'note' => 'Catatan', 'time' => 'Waktu / tahapan', 'principles' => 'Prinsip',
        'proposal_label' => 'Label usulan / rekomendasi', 'level_label' => 'Label jenjang',
        'percentage' => 'Bobot (%)', 'fairness_note' => 'Catatan penilaian yang adil',
        'lines' => 'Isi janji', 'closing' => 'Penutup', 'salutation' => 'Salam',
        'secondary_action' => 'Teks tautan tambahan',
        'action_url' => 'Tujuan tombol utama', 'secondary_action_url' => 'Tujuan tautan tambahan',
        'read_more' => 'Teks pembuka detail', 'image_caption' => 'Keterangan visual',
        'center_label' => 'Identitas diagram misi', 'areas' => 'Lingkup manfaat',
        'chart_label' => 'Label grafik penilaian',
        'read_less' => 'Teks tutup detail', 'emblem_label' => 'Teks emblem janji bersama',
    ];

    public function get(): array
    {
        $defaults = config('dinoyo-way');
        $saved = CatalogSettings::first()?->dinoyo_way_content ?? [];

        // Earlier editors stored a hidden first philosophy paragraph. Keep only the two visible paragraphs.
        $legacyParagraphs = Arr::get($saved, 'philosophy.paragraphs');
        if (is_array($legacyParagraphs) && array_key_exists(2, $legacyParagraphs)) {
            Arr::set($saved, 'philosophy.paragraphs', array_slice($legacyParagraphs, 1, 2));
        }

        // Only known fields can override the page's fixed structure.
        foreach (Arr::dot($defaults) as $path => $default) {
            $value = Arr::get($saved, $path);
            if ((is_string($default) && is_string($value)) || (is_int($default) && is_int($value))) {
                Arr::set($defaults, $path, $value);
            }
        }
        // Earlier versions shared the mission label with the pledge emblem.
        $legacyEmblem = Arr::get($saved, 'mission.center_label');
        if (!Arr::has($saved, 'pledge.emblem_label') && is_string($legacyEmblem)) {
            $defaults['pledge']['emblem_label'] = $legacyEmblem;
            $defaults['mission']['center_label'] = config('dinoyo-way.mission.center_label');
        }
        foreach (config('dinoyo-way.navigation') as $index => $item) {
            $defaults['navigation'][$index]['id'] = $item['id'];
        }

        return $defaults;
    }

    public function editable(array $content): array
    {
        unset($content['hero']['image'], $content['navigation'], $content['navigation_label'], $content['navigation_aria_label']);
        foreach (self::IMAGE_FIELDS as $path) Arr::forget($content, $path);
        foreach (Arr::dot($content) as $path => $value) {
            $key = Arr::last(explode('.', $path));
            if (in_array($key, ['action', 'secondary_action', 'image_alt'], true) || str_ends_with($key, '_url')) {
                Arr::forget($content, $path);
            }
        }

        return $content;
    }

    public static function maxLength(string $path): int
    {
        $key = Arr::last(explode('.', $path));
        if (str_ends_with($path, '_url')) return 2048;
        if (str_contains($path, 'mission.areas.')) return 40;
        if (in_array($key, ['read_more', 'read_less', 'chart_label'])) return 120;
        if (in_array($key, ['center_label', 'emblem_label'])) return 40;
        if (is_numeric($key) || in_array($key, ['description', 'statement', 'lead', 'detail', 'behavior', 'note', 'fairness_note', 'closing'])) {
            return 5000;
        }
        return in_array($key, ['navigation_label', 'label', 'name']) ? 80 : 500;
    }

    public function imageSlots(array $content): array
    {
        $slots = [];
        foreach (self::IMAGE_FIELDS as $key => $path) {
            $section = explode('.', $path)[0];
            $label = $section === 'values'
                ? 'Foto pilar: '.Arr::get($content, str_replace('.image', '.name', $path))
                : ($key === 'philosophy' ? 'Foto filosofi' : 'Foto standar kerja');
            $slots[$key] = [
                'section' => $section, 'label' => $label,
                'url' => $this->imageUrl(Arr::get($content, $path), config('dinoyo-way.'.$path)),
            ];
        }
        return $slots;
    }

    public function heroImageUrl(array $content): string
    {
        return $this->imageUrl($content['hero']['image'], config('dinoyo-way.hero.image'));
    }

    public function imageUrl(string $path, string $default): string
    {
        if (str_starts_with($path, 'public/catalog/dinoyo-way/')) {
            return Storage::disk('public')->url(substr($path, 7));
        }
        if (str_starts_with($path, 'catalog/dinoyo-way/')) {
            $base = rtrim(config('filesystems.disks.r2.url') ?: env('CDN_BASE_URL', ''), '/');
            return $base ? $base.'/'.$path : Storage::disk('r2')->url($path);
        }
        return asset($default);
    }
}
