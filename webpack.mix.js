const mix = require('laravel-mix');

mix
    .copy('node_modules/jquery/dist/jquery.min.js', 'public/jquery.min.js')
    .copy('node_modules/bootstrap/dist/css/bootstrap.min.css', 'public/bootstrap.min.css')
    .copy('node_modules/bootstrap/dist/css/bootstrap.min.css.map', 'public/bootstrap.min.css.map')
    //.copy('node_modules/bootstrap/dist/js/bootstrap.min.js.map', 'public/bootstrap.min.js.map')
    .copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', 'public/bootstrap.bundle.min.js')
    .copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map', 'public/bootstrap.bundle.min.js.map')

    .copy('node_modules/bootstrap-icons/font/bootstrap-icons.css', 'public/bootstrap-icons.css')
    .copyDirectory('node_modules/bootstrap-icons/font/fonts', 'public/fonts')

    .copy('node_modules/bootstrap-select/dist/js/bootstrap-select.min.js', 'public/bootstrap-select.min.js')
    .copy('node_modules/bootstrap-select/dist/js/bootstrap-select.min.js.map', 'public/bootstrap-select.min.js.map')
    .copy('node_modules/bootstrap-select/dist/css/bootstrap-select.min.css', 'public/bootstrap-select.min.css')

    .copyDirectory('node_modules/@browser-logos', 'public/browser-logos')

    .version()
;
