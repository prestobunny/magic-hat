/**
 * Gulp File.
 *
 * @file Defines gulp tasks for the theme.
 * @version 1.0.0
 * @license GPL-3.0-or-later
 */
/*eslint lines-around-comment: 0*/

var gulp = require( 'gulp' ),
	autoprefixer = require( 'autoprefixer' ),
	//browserSync = require( 'browser-sync' ).create(),
	jshint = require( 'gulp-jshint' ),
	postcss = require( 'gulp-postcss' ),
	rename = require( 'gulp-rename' ),
	rtlcss = require( 'gulp-rtlcss' ),
	sass = require( 'gulp-sass' ),
	uglify = require( 'gulp-uglify' ),
	wait = require( 'gulp-wait' ),

	scss = 'assets/sass/',
	css = 'assets/css/',
	js = 'assets/js/';

gulp.task( 'css', function() {
	return (
		gulp.src( scss + 'style.scss' )
		.pipe( wait( 100 ) ) // Fix Sass error bug
		.pipe( sass({ // Compile Sass
			outputStyle: 'compressed'
		}) ).on( 'error', sass.logError )
		.pipe( postcss([ // Add browser prefixes
			autoprefixer( 'last 2 versions', '> 1%' )
		]) )
		.pipe( rename( 'style.min.css' ) )
		.pipe( gulp.dest( css ) )
		.pipe( rtlcss() ) // Create RTL stylesheet
		.pipe( rename( 'style-rtl.min.css' ) )
		.pipe( gulp.dest( css ) )
	);
});

gulp.task( 'js', function() {
	return (
		gulp.src( js + 'src/*.js' )
		.pipe( jshint() )
		.pipe( jshint.reporter( 'default' ) )
		.pipe( uglify() )
		.pipe( rename({suffix: '.min'}) )
		.pipe( gulp.dest( js ) )
	);
});

gulp.task( 'watch', function() {
	/*
	browserSync.init({
		open: 'external',
		proxy: 'presto.test',
		port: 8080
	});
	*/
	gulp.watch( scss + '**/*.scss', gulp.series( 'css' ) );
	gulp.watch( js + 'src/*.js', gulp.series( 'js' ) );
});

gulp.task( 'default', gulp.series( 'watch' ) );
