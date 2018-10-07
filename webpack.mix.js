let mix = require('laravel-mix');

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
	}
 });

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .styles([
		'node_modules/weather-icons/css/weather-icons.css',
		'node_modules/pnotify/dist/pnotify.css',
		'node_modules/pnotify/dist/pnotify.brighttheme.css',
		'node_modules/@fancyapps/fancybox/dist/jquery.fancybox.css',
		'public/css/app.css',
   	], 'public/css/app.css')
   .copy('resources/img/', 'public/img')
   .copy('node_modules/intl-tel-input/build/img/', 'public/img')
   .copy('resources/sounds/', 'public/sounds')
   .copy('node_modules/weather-icons/font', 'public/font')
   .sourceMaps()
   .version();
