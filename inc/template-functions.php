<?php
/**
 * Template Functions
 *
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package Magic Hat
 * @subpackage Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'magic_hat_auto_title' ) ) :
/**
 * Adds a title if a post is saved without one. By default the function will try to use
 * an excerpt up to 50 characters of the post content as the title, but if this is also
 * empty, the post title will be set to "Post {$id}".
 *
 * @since 1.0.0
 *
 * @param int $post_id	ID of the post being updated.
 * @param WP_Post $post Post object being updated.
 */
function magic_hat_auto_title( $post_id, $post ) {
	if ( ! wp_is_post_revision( $post_id ) && empty( $post->post_title ) ) {
		$content = trim( strip_tags( $post->post_content ) );
		$title = substr( $content, 0, 50 );
		/* translators: default post title based on post ID, e.g. "Post 123" */
		$title = empty( $title ) ? sprintf( esc_html__( 'Post %d', 'magic-hat' ), $post_id ) : $title;
		$title .= strlen( $content ) > 50 ? '...' : '';

		$post_format = get_post_format( $post );
		/**
		 * Filters the post title which gets automatically added to quotes, status posts,
		 * and asides. The default title is the first fifty characters of the content.
		 *
		 * @since 1.0.0
		 *
		 * @param string $title			The title to filter.
		 * @param WP_Post $post			The post object to add the title to.
		 * @param string $post_format	The post format of the current post.
		 * @return string				The filtered post title.
		 */
		$title = apply_filters( 'magic_hat_post_format_auto_title', $title, $post, $post_format );

		/* Prevent infinite loop */
		remove_action( 'save_post', 'magic_hat_auto_title', 10, 2 );

		wp_update_post(array(
			'ID' => $post_id,
			'post_title' => $title,
		) );

		add_action( 'save_post', 'magic_hat_auto_title', 10, 2 );
	}
}
endif;
add_action( 'save_post', 'magic_hat_auto_title', 10, 2 );

if ( ! function_exists( 'magic_hat_remove_styles' ) ) :
/**
 * Removes some default WordPress styles.
 *
 * @since 1.0.0
 */
function magic_hat_remove_styles() {
	wp_dequeue_style( 'wp-block-library' );
}
endif;
add_action( 'wp_print_styles', 'magic_hat_remove_styles', 100 );

if ( ! function_exists( 'magic_hat_body_classes' ) ) :
/**
 * Adds custom classes to the array of body classes.
 *
 * @since 1.0.0
 *
 * @param array $classes Classes for the body element.
 * @return array		 Updated body class array.
 */
function magic_hat_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
endif;
add_filter( 'body_class', 'magic_hat_body_classes' );

if ( ! function_exists( 'magic_hat_term_links_tags' ) ) :
/**
 * Adds a hashtag before tags in a tag list. The term_links-{$taxonomy} filter is
 * necessary in order to get the pound sign inside the enclosing anchor element.
 *
 * @see get_the_term_list
 *
 * @param array $links	List of tags as formatted HTML strings.
 * @return array		The list of tags with a hash tag prepended.
 */
function magic_hat_term_links_tags( $links ) {
    $wrapped_term_links = array();
	foreach ( $links as &$link ) {
		$link = str_replace(
			array(
				'rel="tag">',
				'<a href="',
			),
			array(
				'rel="tag">#', // Add hashtag
				'<a class="meta__tag" href="', // Add class "meta__tag"
			),
			$link
		);
	}
	return $links;
}
endif;
add_filter( 'term_links-post_tag', 'magic_hat_term_links_tags' );

/**
 * Filters the string that gets added to excerpts before the <!-- more --> tag, which
 * is '[...]' by default. This returns an ellipsis without brackets.
 *
 * @since 1.0.0
 *
 * @param string $more	The default more string
 * @return string		An ellipsis
 */
function magic_hat_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'magic_hat_excerpt_more' );

/**
 * Filters the length of post excerpts.
 *
 * @since 1.0.0
 *
 * @param int $length	Number of words.
 * @return int			Filtered number of words.
 */
function magic_hat_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'magic_hat_excerpt_length' );

if ( ! function_exists( 'magic_hat_excerpt_password' ) ) :
/**
 * Shows the password form in post excerpts.
 *
 * @since 1.0.0
 *
 * @param string $content	The excerpt content to show.
 * @return string			A password form for the protected post.
 */
function magic_hat_excerpt_password( $content ) {
	if ( post_password_required() ) {
		$content = get_the_password_form();
	}

	return $content;
}
endif;
add_filter( 'the_excerpt', 'magic_hat_excerpt_password' );

/**
 * Changes the prefix on password-protected posts from "Protected:" to "Locked:" or
 * "Unlocked:" if the password has been entered already.
 *
 * @since 1.0.0
 *
 * @param string $title		Post title. Uses %s for the name of the post.
 * @param WP_Post $post		Current post object.
 * @return string			Post title with new prefix.
 */
function magic_hat_protected_title_format( $title, $post ) {
	if ( post_password_required( $post ) ) {
		/* translators: %s is the title of the protected post. */
		return esc_html__( 'Locked: %s', 'magic-hat' );
	} else {
		/* translators: %s is the title of the unlocked, protected post. */
		return esc_html__( 'Unlocked: %s', 'magic-hat' );
	}
}
add_filter( 'protected_title_format', 'magic_hat_protected_title_format', 10, 2 );

/**
 * Filters the sizes of different tags in the Tag Cloud Widget.
 *
 * @param array $args	Array of size names and unitless font sizes.
 * @return array		Filtered array.
 */
function magic_hat_tag_cloud_sizes( $args ) {
    $args = array(
		'smallest'=> .9,
		'default' => 1,
		'largest' => 1.2,
		'unit' => 'rem'
	);
    return $args;
}
add_filter( 'widget_tag_cloud_args','magic_hat_tag_cloud_sizes' );

/**
 * Removes the WordPress.org link from the Meta Widget.
 *
 * @since 1.0.0
 *
 * @param string $content	Content of the WordPress.org link.
 * @return string			Filtered WordPress.org link.
 */
function magic_hat_meta_poweredby( $content ) {
	return '';
}
add_filter( 'widget_meta_poweredby', 'magic_hat_meta_poweredby' );
