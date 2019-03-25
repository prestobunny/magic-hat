<?php
/**
 * Magic Hat Bootstrap File
 *
 * Wow, this is where all the magic happens! To make your own theme based on Magic Hat,
 * find and replace of all instances of 'magic_hat' and 'magic-hat', including in the
 * javascript files. Or don't, and just call it Magic Hat again! :D
 *
 * @package Magic Hat
 * @since 1.0.0
 */

$magic_hat_directory = get_template_directory();

/**
 * Functions used by the theme. Don't remove these!
 */
require $magic_hat_directory . '/inc/template-tags.php';
require $magic_hat_directory . '/inc/template-functions.php';

/**
 * Media functions to modify various built-in shortcodes. Don't remove this!
 */
require $magic_hat_directory . '/inc/media-functions.php';

/**
 * Comment functions and script calls. Don't remove!
 */
require $magic_hat_directory . '/inc/comment-functions.php';

/**
 * Customizer additions. Remove this line to remove theme options and customizer support.
 */
include $magic_hat_directory . '/inc/customizer.php';

/**
 * Breadcrumb plug-in by Justin Tadlock. Remove this line to disable breadcrumbs.
 */
include $magic_hat_directory . '/inc/class-breadcrumb-trail.php';

/**
 * Post Format support. Remove this line to remove all post format functionality.
 */
include $magic_hat_directory . '/inc/post-formats.php';

/**
 * Plugin support. Remove this line to remove support for Jetpack, WooCommerce and Soil.
 */
include $magic_hat_directory . '/inc/plugins.php';

if ( ! function_exists( 'magic_hat_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since 1.0.0
 */
function magic_hat_setup() {
	load_theme_textdomain( 'magic-hat', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form', ) );

	/*
	Add starter content to better show off your theme in the Customizer.
	https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
	add_theme_support( 'starter-content', array() );
	*/

	/*
	Gutenberg
	https://wordpress.org/gutenberg/handbook/designers-developers/developers/themes/theme-support/
	*/
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	/*
	Add editor styles
	add_theme_support( 'editor-styles' );
	add_theme_support( 'dark-editor-style' );
	*/

	/*
	Add font size presets for the block editor.
	add_theme_support( 'disable-custom-font-sizes' );
	add_theme_support( 'editor-font-sizes', array(
		array(
			'name' => __( 'Small', 'themeLangDomain' ),
			'size' => 12,
			'slug' => 'small'
		),
	) );
	*/

	//add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'editor-color-palette', array() );

	register_nav_menus( array(
		'menu-main' => esc_html__( 'Main Menu', 'magic-hat' ),
		'menu-top' => esc_html__( 'Topbar Menu', 'magic-hat' ),
		'menu-footer' => esc_html__( 'Footer Menu', 'magic-hat' ),
	) );
}
endif;
add_action( 'after_setup_theme', 'magic_hat_setup' );

if ( ! function_exists( 'magic_hat_theme_credit' ) ) :
/**
 * Prints the theme author name/link. It's called in the footer.
 *
 * @since 1.0.0
 */
function magic_hat_theme_credit() {
	/* translators: %s is the name of the theme author. */
	echo wp_kses_post( '<br /><span class="legal-credit">' . sprintf( __( 'Theme by %s', 'magic-hat' ), '<a class="legal-credit__link" href="https://presto.blog">Presto!</a>' ) . '</span>' );
}
endif;

/**
 * Sets the content width global so it's available for plugins. I don't actually know what
 * the point of this is. It seems to hail from a time before CSS...
 *
 * @since 1.0.0
 *
 * @global int $content_width
 */
function magic_hat_content_width() {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'magic_hat_content_width', 640 );
}
add_action( 'after_setup_theme', 'magic_hat_content_width', 0 );

/**
 * Registers widget areas.
 *
 * @see register_sidebar()
 *
 * @since 1.0.0
 */
function magic_hat_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Header Widget', 'magic-hat' ),
		'id'            => 'header',
		'description'   => esc_html__( 'Add a widget next to your logo. Maybe an ad, social media links, or a search bar.', 'magic-hat' ),
		'before_widget' => '<section id="%1$s" class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="screen-reader-text">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'magic-hat' ),
		'id'            => 'sidebar',
		'description'   => esc_html__( 'Add widgets to the sidebar.', 'magic-hat' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Info Widget', 'magic-hat' ),
		'id'            => 'info',
		'description'   => esc_html__( 'Add a widget above the copyright text.', 'magic-hat' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget', 'magic-hat' ),
		'id'            => 'footer',
		'description'   => esc_html__( 'Add a widget next to the copyright text. Similar to the header widget space.', 'magic-hat' ),
		'before_widget' => '<section id="%1$s" class="%2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'magic_hat_widgets_init' );

if ( ! function_exists( 'magic_hat_enqueue_scripts' ) ) :
/**
 * Enqueues Magic Hat's stylesheet and scripts. This function is pluggable in case you
 * want to customize what gets enqueued and how it's added.
 *
 * @since 1.0.0
 */
function magic_hat_enqueue_scripts() {
	wp_deregister_style( 'mediaelement' );
	wp_deregister_style( 'wp-mediaelement' );

	/* Html5shiv */
	wp_enqueue_style( 'html5shiv', 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js', array(), false, true );
	wp_enqueue_style( 'html5shiv-printshiv', 'https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv-printshiv.min.js', array(), false, true );

	/* Hey kids! Put your custom fonts here! */
	wp_enqueue_style( 'magic-hat-fonts', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:900' );

	/* Minified Stylesheet */
	$style = is_rtl() ? '/assets/css/style-rtl.min.css' : '/assets/css/style.min.css';
	wp_enqueue_style( 'magic-hat-style', get_template_directory_uri() . $style, array(), null );

	/* Gutenberg print styles */
	if ( get_theme_mod( 'use-printstyles', 0 ) ) {
		wp_enqueue_style( 'bafs-gutenberg', 'https://unpkg.com/gutenberg-css@0.4.7/dist/gutenberg.min.css', array(), null, 'print' );
	}

	/* Dropdown navigation and skip link focus fix */
	wp_enqueue_script( 'magic-hat-navigation', get_template_directory_uri() . '/assets/js/navigation.min.js', array('jquery'), false, true );
}
endif;
add_action( 'wp_enqueue_scripts', 'magic_hat_enqueue_scripts' );
