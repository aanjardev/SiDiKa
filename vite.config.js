import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/',

    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                // Page entries
                "resources/js/penjualan/penjualan.js",

                "resources/js/pembelian/pembelian.js",
                "resources/js/qualityControl/data-qc.js",
                // Standalone utilities referenced directly via @vite in Blade
                "resources/js/utils/clickable-rows.js",
                "resources/js/utils/handle-delete.js",
                "resources/js/utils/search.js",
                "resources/js/utils/phone-input-validation.js",

                // == ASET ADMIN (BARU) ==
                // CSS Admin
                "resources/admin_theme/css/core/libs.min.css",
                "resources/admin_theme/vendor/aos/dist/aos.css",
                "resources/admin_theme/css/hope-ui.min.css",
                "resources/admin_theme/css/custom.min.css",
                "resources/admin_theme/css/dark.min.css",
                "resources/admin_theme/css/customizer.min.css",
                "resources/admin_theme/css/rtl.min.css",

                // // JS Admin
                // "resources/admin_theme/js/core/libs.min.js",
                // "resources/admin_theme/js/core/external.min.js",
                // // "resources/admin_theme/js/plugins/setting.js",
                // "resources/admin_theme/js/hope-ui.js",
            ],
            refresh: true,
        }),
    ],
});
