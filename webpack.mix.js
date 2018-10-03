let mix = require('laravel-mix');
let ImageminPlugin = require('imagemin-webpack-plugin').default;
let CopyWebpackPlugin = require('copy-webpack-plugin');
let imageminMozjpeg = require('imagemin-mozjpeg');

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
 mix.webpackConfig({
 	module: {
		rules: [
		  {
		    test: /\.modernizrrc.js$/,
		    use: [ 'modernizr-loader' ]
		  },
		  {
		    test: /\.modernizrrc(\.json)?$/,
		    use: [ 'modernizr-loader', 'json-loader' ]
		  }
		]
		},
	resolve: {
		alias: {
		  modernizr$: path.resolve(__dirname, ".modernizrrc")
		}
	},
	plugins: [
        new CopyWebpackPlugin([{
            from: 'resources/img',
            to: 'img',
        }]),
        new CopyWebpackPlugin([{
            from: 'node_modules/intl-tel-input/build/img',
            to: 'img',
        }]),
        new ImageminPlugin({
            test: /\.(jpe?g|png|gif|svg)$/i,
            plugins: [
                imageminMozjpeg({
                    quality: 80,
                })
            ]
        })
    ]
 });

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .styles([
		'node_modules/weather-icons/css/weather-icons.css',
		'node_modules/pnotify/dist/pnotify.css',
		'node_modules/pnotify/dist/pnotify.brighttheme.css',
		'public/css/app.css',
   	], 'public/css/app.css')
   .copy('resources/img/', 'public/img')
   .copy('resources/sounds/', 'public/sounds')
   .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts')
   .copy('node_modules/weather-icons/font', 'public/font')
   .sourceMaps()
   .version();
