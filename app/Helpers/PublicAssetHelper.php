<?php

namespace App\Helpers;

class PublicAssetHelper
{
    /** Resolve bundled assets for the existing Hostinger subdirectory layout. */
    public static function url(string $path): string
    {
        $path = ltrim($path, '/');
        $url = asset((app()->environment('production') ? 'public/' : '').$path);
        $file = public_path($path);

        // A content version prevents the CDN/browser from reusing an older deployment.
        if (is_file($file)) {
            $version = substr(hash_file('sha256', $file), 0, 12);
            $url .= '?v='.$version;
        }

        return $url;
    }
}
