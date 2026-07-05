let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management	混合资产管理
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 | Mix 提供了一个简洁流畅的 API，用于为你的 Laravel Laravel 应用定义一些 Webpackpack 构建步骤。
 | 默认情况下，我们会为应用程序编译 Sass 文件，并打包所有 JS 文件。
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');
