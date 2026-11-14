<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class DinoyoWayContent
{
    public function get(): array
    {
        return config('dinoyo-way');
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

        return \App\Helpers\PublicAssetHelper::url($default);
    }

    public function heroImageUrl(array $content): string
    {
        return $this->imageUrl($content['hero']['image'], config('dinoyo-way.hero.image'));
    }

    public function philosophyImageUrl(array $content): string
    {
        return $this->imageUrl($content['philosophy']['image'], config('dinoyo-way.philosophy.image'));
    }
}
