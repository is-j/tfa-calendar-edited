const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ]);
mix.version(['public/js/dashboard.js','public/js/settings.js','public/js/config.js','public/js/cancel.js','public/js/auth/register.js','public/js/admin/config.js','public/js/admin/users.js','public/js/admin/reports.js', 'public/js/admin/subjects.js']);
