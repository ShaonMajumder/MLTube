const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .autoload({
        jquery: ['$', 'jQuery'],
    })
    // .sass('resources/sass/app.scss', 'public/css') // points to app.css
    .sass('resources/sass/theme-light.scss', 'public/css')
    .sass('resources/sass/theme-dark.scss', 'public/css')
    .styles([
        'resources/css/app.css',
        'resources/css/common.css',
        'resources/css/theme-dark.css',
        'resources/css/theme-light.css',
    ], 'public/css/theme.css')
    .version(); // points to theme.css