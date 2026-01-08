import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/',

    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",

                "resources/js/penjualan/penjualan.js",

                "resources/js/pembelian/pembelian.js",
                "resources/js/qualityControl/data-qc.js",

                "resources/js/utils/clickable-rows.js",
                "resources/js/utils/handle-delete.js",
                "resources/js/utils/search.js",
                "resources/js/utils/phone-input-validation.js",
                "resources/js/admin/catalog-settings.js",


                "resources/admin_theme/css/core/libs.min.css",
                "resources/admin_theme/vendor/aos/dist/aos.css",
                "resources/admin_theme/css/hope-ui.min.css",
                "resources/admin_theme/css/custom.min.css",
                "resources/admin_theme/css/dark.min.css",
                "resources/admin_theme/css/customizer.min.css",
                "resources/admin_theme/css/rtl.min.css",





            ],
            refresh: true,
        }),
    ],
});
