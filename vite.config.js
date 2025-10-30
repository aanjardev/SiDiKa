import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",

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
