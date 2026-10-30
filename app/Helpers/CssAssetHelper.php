<?php

namespace App\Helpers;

class CssAssetHelper
{
    /**
     * Get CSS asset path based on environment
     *
     * Local (development): asset('css/legacy/file.css')
     * Production (Hostinger): asset('public/css/legacy/file.css')
     *
     * @param string $path CSS file path (e.g., 'css/legacy/mainPage.css')
     * @return string Full asset URL
     */
    public static function css($path)
    {
        return PublicAssetHelper::url($path);
    }
}
