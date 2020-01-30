const mix = require('laravel-mix');

mix.sass(
	'Assets/Scss/main.scss',
	'Resources/Public/'
).options({
	processCssUrls: false
});
