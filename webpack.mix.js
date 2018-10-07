let mix = require('laravel-mix');                           // If you are new to this then please visit https://laravel.com/docs/5.5/mix
const webpack = require('webpack');

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

var plugin =  'resources/plugins/';
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
const CompressionPlugin = require('compression-webpack-plugin');

mix.js('resources/js/app.js', 'public/js/app.js')
  .combine([
    plugin + 'jquery/jquery.min.js',
    plugin + 'popper/popper.min.js',
    plugin + 'bootstrap/bootstrap.min.js',
    plugin + 'sidebarmenu.js',
    plugin + 'moment/moment.min.js',
    plugin + 'toastr/toastr.min.js',
    plugin + 'slimscroll/jquery.slimscroll.js',
    plugin + 'waves/waves.js',
    plugin + 'sticky-kit/sticky-kit.min.js',
    plugin + 'autosize/autosize.min.js',
    plugin + 'summernote/summernote-bs4.js',
    'resources/js/custom.js',
  ],'public/js/plugin.js')
    .sass('resources/sass/style.scss', 'public/css')
    .sass('resources/sass/style-rtl.scss', 'public/css')
    .sass('resources/sass/colors/blue.scss', 'public/css/colors')
    .sass('resources/sass/colors/blue-dark.scss', 'public/css/colors')
    .sass('resources/sass/colors/default.scss', 'public/css/colors')
    .sass('resources/sass/colors/default-dark.scss', 'public/css/colors')
    .sass('resources/sass/colors/green.scss', 'public/css/colors')
    .sass('resources/sass/colors/green-dark.scss', 'public/css/colors')
    .sass('resources/sass/colors/megna.scss', 'public/css/colors')
    .sass('resources/sass/colors/megna-dark.scss', 'public/css/colors')
    .sass('resources/sass/colors/purple.scss', 'public/css/colors')
    .sass('resources/sass/colors/purple-dark.scss', 'public/css/colors')
    .sass('resources/sass/colors/red.scss', 'public/css/colors')
    .sass('resources/sass/colors/red-dark.scss', 'public/css/colors')
    .browserSync('laravue')                     // this is the alias/virtual host which will be called to load http://localhost:3000
    .webpackConfig({
        devtool: "cheap-module-source-map",     // "eval-source-map" or "inline-source-map" or "cheap-module-source-map" or "eval"
        plugins: [
            // new BundleAnalyzerPlugin(),      // load this package to see which plugins with its size detail
            new CompressionPlugin({             // very import to compress the assets
                asset: "[path].gz[query]",
                algorithm: "gzip",
                test: /\.js$|\.css$|\.html$|\.svg$/,
                threshold: 10240,
                minRatio: 0.8
            }),
            new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
        ]
    });

if (mix.inProduction()) {                       // In production environtment use versioning
    mix.version();
}
