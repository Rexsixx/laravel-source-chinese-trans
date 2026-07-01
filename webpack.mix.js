const mix = require('laravel-mix');

/**
 * webpack
 */

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management	混合资产管理
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 | Mix 为你的 Laravel 应用程序提供了一套简洁、流畅的 API，用于定义一些 Webpack Webpack 构建步骤。
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');
