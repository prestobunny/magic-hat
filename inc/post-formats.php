<?php
/**
 * Support for post formats.
 * 
 * @since 1.0.0
 * @package Magic Hat
 * @subpackage Admin
 */

/**
 * Registers theme support for all post formats except asides. The intention with this
 * implementation is to mimic Tumblr, which has no asides equivalent. The status format
 * is included since it covers the purpose of asides but is more intuitive given the
 * widespread use of sites like Facebook and Twitter.
 * 
 * @since 1.0.0
 */
function magic_hat_support_post_formats() {
	/**
	 * Filters the post formats supported by this theme. The default is all post formats
	 * except asides.
	 * 
	 * @link https://codex.wordpress.org/Post_Formats
	 * 
	 * @param array		The post formats to support.
	 */
	add_theme_support( 'post-formats', apply_filters( 'magic_hat_support_post_formats', array(
		'audio',
		'chat',
		'gallery',
		'image',
		'link',
		'quote',
		'status',
		'video',
	) ) );
}
add_action( 'after_setup_theme', 'magic_hat_support_post_formats' );