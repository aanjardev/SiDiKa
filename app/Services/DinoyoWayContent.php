<?php

namespace App\Services;

use App\Models\CatalogSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DinoyoWayContent
{
    public const SECTIONS = ['hero' => 'Hero', 'philosophy' => 'Filosofi', 'spiritual' => 'Landasan spiritual', 'vision_mission' => 'Visi misi', 'culture' => 'Budaya organisasi', 'discipline' => 'Disiplin sukses', 'dk_way' => 'DK Way', 'psychotest' => 'Tes Psikotes', 'secret_potion' => 'Secret potion'];

    public function get(): array
    {
        $defaults = config('dinoyo-way');
        $stored = CatalogSettings::first()?->dinoyo_way_content;
        return array_replace_recursive($defaults, is_array($stored) ? $stored : []);
    }

    public function editable(array $content): array
    {
        return [
            'title' => $content['title'], 'navigation_label' => $content['navigation_label'], 'description' => $content['description'], 'navigation_aria_label' => $content['navigation_aria_label'],
            'navigation' => array_map(fn ($item) => ['label' => $item['label']], $content['navigation']),
            'hero' => Arr::only($content['hero'], ['eyebrow', 'title', 'statement', 'description', 'image_alt', 'caption']),
            'philosophy' => Arr::only($content['philosophy'], ['eyebrow', 'title', 'lead', 'paragraphs', 'image_alt', 'image_caption']),
            'spiritual' => Arr::only($content['spiritual'], ['eyebrow', 'title', 'arabic_segments', 'translation', 'reference', 'note']),
            'vision_mission' => Arr::only($content['vision_mission'], ['eyebrow', 'title', 'vision_label', 'vision', 'mission_label', 'missions']),
            'culture' => Arr::only($content['culture'], ['eyebrow', 'title', 'description', 'items', 'indicators']),
            'discipline' => Arr::only($content['discipline'], ['eyebrow', 'title', 'groups']),
            'dk_way' => Arr::only($content['dk_way'], ['eyebrow', 'title', 'definition_label', 'definition', 'how_to_label', 'how_to', 'winning_label', 'jihad_label', 'jihad_time', 'jihad_description']),
            'psychotest' => Arr::only($content['psychotest'], ['eyebrow', 'title', 'description', 'action']),
            'secret_potion' => Arr::only($content['secret_potion'], ['eyebrow', 'title', 'description', 'action']),
        ];
    }

    public static function maxLength(string $path): int
    {
        return preg_match('/(description|meaning|application|definition|vision|lead|note|translation|jihad_description)$/', $path) ? 1000 : 255;
    }

    public function imageUrl(string $path, string $default): string
    {
        if (str_starts_with($path, 'public/catalog/dinoyo-way/')) return Storage::disk('public')->url(substr($path, 7));
        if (str_starts_with($path, 'catalog/dinoyo-way/')) {
            $base = rtrim(config('filesystems.disks.r2.url') ?: env('CDN_BASE_URL', ''), '/');
            return $base ? $base.'/'.$path : Storage::disk('r2')->url($path);
        }
        return \App\Helpers\PublicAssetHelper::url($default);
    }

    public function heroImageUrl(array $content): string { return $this->imageUrl($content['hero']['image'], config('dinoyo-way.hero.image')); }
    public function philosophyImageUrl(array $content): string { return $this->imageUrl($content['philosophy']['image'], config('dinoyo-way.philosophy.image')); }
}
