
const mix = require('laravel-mix');
const path = require('path');

// Ajusta el prefijo de los assets para despliegue en subcarpeta
mix.setResourceRoot('/inf513/grupo09sa/churrasquera/sistema-web/public/');
mix.setPublicPath('public');

mix.js('resources/js/app.js', 'public/js')
    .vue({ version: 3 })
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
            }
        }
    })
    .version();
