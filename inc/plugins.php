<?php
/**
 * Plugin Compatibility File
 *
 * Functions which setup compatibility between various plugins and the theme.
 *
 * @package Magic Hat
 * @subpackage Plugins
 * @since 1.0.0
 */

/**
 * WooCommerce support.
 */
/*
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
}
*/

if ( ! function_exists( 'magic_hat_support_plugins' ) ) :
function magic_hat_support_plugins() {
	/**
	 * Jetpack Support
	 * @link https://jetpack.com/
	 */

	if ( defined( 'JETPACK__VERSION' ) ) {
		add_theme_support( 'social-links', array(
    	'facebook', 'twitter', 'linkedin', 'google_plus', 'tumblr',
		) );
		/**
		 * Responsive Videos
		 * @link https://jetpack.com/support/responsive-videos/
		 */
		add_theme_support( 'jetpack-responsive-videos' );

		/**
		 * Content Options
		 * @link https://jetpack.com/support/content-options/
		 */
		add_theme_support( 'jetpack-content-options', array(
			'post-details'    => array(
				'stylesheet' => 'magic-hat-style',
				'date'       => '.meta-date',
				'categories' => '.meta-categories',
				'tags'       => '.meta-tags',
				'comment'    => '.meta-comments',
			),
			'featured-images' => array(
				'archive'    => true,
				'post'       => true,
				'page'       => true,
			),
		) );
	}

	/**
	 * Soil Support
	 * @link https://roots.io/plugins/soil/
	 */
	add_theme_support('soil-clean-up');
	add_theme_support('soil-disable-rest-api');
	add_theme_support('soil-disable-trackbacks');
	add_theme_support('soil-js-to-footer');
	add_theme_support('soil-nice-search');
	add_theme_support('soil-relative-urls');
	//add_theme_support('soil-nav-walker');
	//add_theme_support('soil-disable-asset-versioning');
	//add_theme_support('soil-google-analytics', 'UA-XXXXX-Y');
	add_theme_support('soil-jquery-cdn');
}
endif;
add_action( 'after_setup_theme', 'magic_hat_support_plugins' );
remove_filter('embed_oembed_html', 'Roots\Soil\CleanUp\\embed_wrap');

/**
 * Removes the sharing buttons from posts in archive display.
 *
 * @since 1.0.0
 */
function magic_hat_jetpack_sharing() {
	remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
add_action( 'loop_start', 'magic_hat_jetpack_sharing' );
