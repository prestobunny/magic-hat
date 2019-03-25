<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features. Optimism!
 *
 * @package Magic Hat
 * @subpackage Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'magic_hat_get_post_template' ) ) :
/**
* Loads the template part for custom post type or the post format if applicable.
*
* @since 1.0.0
*/
function magic_hat_get_post_template() {
	$format = get_post_format() ?: 'standard';
	if ( get_theme_support( 'post-formats' ) && class_exists( 'Presto_Post_Formats' ) && $format !== 'standard' ) {
		get_template_part( 'template-parts/content', $format );
	} else {
		get_template_part( 'template-parts/content', get_post_type() );
	}
}
endif;

if ( ! function_exists( 'magic_hat_entry_header' ) ) :
/**
 * Prints the post title wrapped in the appropriate heading/link tags depending on whether
 * the current view is in an archive or a singular context.
 *
 * @since 1.0.0
 *
 * @param string $title		The title to wrap in tags. Default is post title returned by
 * 							{@see get_the_title}.
 * @param int $id			The ID of the post to use in ID attributes. Default get_the_ID().
 */
function magic_hat_entry_header( $title = null, $id = null ) {
	$title = $title ?: get_the_title();
	$id = $id ?: get_the_id();

	$output = wp_kses_post( $title );
	if ( is_singular() ) {
		$output = "
		<h1 class=\"entry__title\">{$output}</h1>";
	} else {
		$output = '
		<h2 class="entry__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $output . '</a></h2>';
	}

	$meta = '';
	if ( 'page' !== get_post_type() ) {
		$meta = '<div class="entry__meta entry__meta-header">' . magic_hat_get_entry_meta() . '</div>';
	}

	$output = "
	<header class=\"entry__header\" id=\"entry__header-{$id}\">
		{$output}
		{$meta}
	</header>";

	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
endif;

if ( ! function_exists( 'magic_hat_get_entry_meta' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time.
 *
 * @return string	HTML output with post date and optionally comment number, permalink,
 * 					and category.
 */
function magic_hat_get_entry_meta() {
	$posted = sprintf( '<time datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	$updated = false;
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$updated = sprintf(
			'<time class="meta meta-date meta-date-updated" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);
	}

	/* translators: %s: post date. */
	$output = '<span class="meta meta-date meta-date-posted">' . sprintf( esc_html__( 'Posted on %s', 'magic-hat' ), $posted ) . '<br /></span>';

	if ( ! is_singular() ) {
		$category = get_the_category();
		if ( ! empty( $category ) ) {
			$output .= '<span class="meta meta-category"><a href="' . get_category_link( $category[0]->term_id ) . '">' . $category[0]->name . '</a></span>';
		}
	}

	$permalink = get_permalink();

	if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		/* translators: %d is the number of comments. */
		$output .= '<span class="meta meta-comments"><a href="' . $permalink . '#comments">' . sprintf( esc_html__( '%d comments', 'magic-hat' ), get_comments_number() ) . '</a></span>';
	}

	$output .= '<span class="meta meta-permalink"><a href="' . $permalink . '">' . esc_html__( 'Permalink', 'magic-hat' ) . '</a></span>';

	$output .= sprintf(
		'<a class="meta meta-link meta-link-edit" href="%s">' .
		wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Edit<span class="screen-reader-text"> %s</span>', 'magic-hat' ),
			array(
				'span' => array(
					'class' => array(),
				),
			)
		) .
		'</a>',
		get_edit_post_link(),
		get_the_title()
	);

	if ( is_single() && $updated ) {
		/* translators: %s: post date. */
		$output .= '<span class="meta meta-date meta-date-updated"><br />' . sprintf( esc_html__( 'Last updated %s', 'magic-hat' ), $updated ) . '</span>';
	}
	return $output;
}
endif;

if ( ! function_exists( 'magic_hat_sticky_ribbon' ) ) :
/**
 * Prints the markup for a corner ribbon that says "Featured" on sticky posts. If you
 * remove this element without replacing it with some form of visual sticky post
 * distinction, remember to remove "sticky-posts" from the theme tags in style.css.
 *
 * @since 1.0.0
 */
function magic_hat_sticky_ribbon() {
	if ( is_sticky() ) { ?>
		<div class="sticky-ribbon"><span><?php esc_html_e( 'Featured', 'magic-hat' ); ?></span></div>
	<?php }
}
endif;

if ( ! function_exists( 'magic_hat_entry_footer' ) ) :
/**
 * Prints HTML with meta information on the post's categories and tags.
 *
 * @since 1.0.0
 */
function magic_hat_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		$tags_list = get_the_tag_list();
		if ( $tags_list ) {
			printf( '<p class="meta meta-tags">%s</p>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'magic-hat' ) );
		if ( $categories_list ) {
			/* translators: 1: list of categories. */
			printf( '<span class="meta meta-categories">' . esc_html__( 'Posted in %1$s', 'magic-hat' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Edit<span class="screen-reader-text"> %s</span>', 'magic-hat' ),
				array(
					'span' => array(
						'class' => array(),
					),
				)
			),
			get_the_title()
		),
		'<span class="meta meta-link meta-link-edit">',
		'</span>'
	);
}
endif;

if ( ! function_exists( 'magic_hat_post_thumbnail' ) ) :
/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * @since 1.0.0
 */
function magic_hat_post_thumbnail() {
	if ( is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) { ?>
		<div class="entry__thumbnail">
			<?php the_post_thumbnail(); ?>
		</div><!-- .post-thumbnail -->
	<?php } else { ?>
		<a class="entry__thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			the_post_thumbnail( 'post-thumbnail', array(
				'alt' => the_title_attribute( array(
					'echo' => false,
				) ),
			) );
			?>
		</a>
	<?php }
}
endif;

if ( ! function_exists( 'magic_hat_content_excerpt' ) ) :
/**
 * Echoes the post content or the excerpt of the post depending on whether the current
 * page is a singular or archive view.
 *
 * @see the_content
 * @see the_excerpt
 *
 * @since 1.0.0
 */
function magic_hat_content_excerpt() {
	global $post;
	if ( is_singular() ) {
		the_content();
		magic_hat_link_pages();
	} else {
		/* Use custom excerpt if the user added a <!-- more --> tag */
		if ( strpos( $post->post_content, '<!--more-->' ) ) {
			the_content( sprintf( wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'magic-hat' ),
				array( 'span' => array( 'class' => array() ) ) ),
				get_the_title()
			) );
		}
		else {
			the_excerpt();
		}
	}
}
endif;

if ( ! function_exists( 'magic_hat_paginate_links' ) ) :
/**
 * Prints {@see paginate_links} with disabled versions of the previous/next links on the
 * first and last pages, respectively, and echoes the result. It also adds aria labels.
 *
 * Feel free to plug your own function instead if you don't like this one. You can use
 *
 *    function magic_hat_paginate_links( $args = array(), $type = 'posts) {
 *        if ( $type == 'posts' ) {
 *            echo paginate_links( $args );
 *        } else {
 *            paginate_comments_links( $args );
 *        }
 *    }
 *
 * ... or something similar to use the default WordPress pagination without having to
 * override the template files that call this function.
 *
 * @since 1.0.0
 * @todo Single post pagination?
 *
 * @see get_the_posts_pagination
 * @see wp_link_pages
 * @see paginate_comments_links
 *
 * @param array $args
 * @param string $type	What the pages are for. Default 'posts'. Accepts 'posts', 'post',
 * 						or 'comments'.
 */
function magic_hat_paginate_links( $args = array(), $type = 'posts' ) {
	global $wp_query, $wp_rewrite;

	$args['prev_next'] = true;
	$args['type'] = 'array';
	$args['prev_text'] = isset( $args['prev_text'] ) ? esc_attr( $args['prev_text'] ) : '&lsaquo;';
	$args['next_text'] = isset( $args['next_text'] ) ? esc_attr( $args['next_text'] ) : '&rsaquo;';
	$args['before_page_number'] = '<span class="screen-reader-text">' . esc_html__( 'Page', 'magic-hat' ) . ' </span>';

	if ( $type == 'posts' ) {
		/* Post pagination */
		$total = isset( $wp_query->max_num_pages ) ? $wp_query->max_num_pages : 1;
		if ( $total <= 1 ) {
			return;
		}
		$current = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
		$nav_atts = 'id="posts-nav" class="nav-posts" aria-label="' . esc_html__( 'Post archive navigation', 'magic-hat' ) . '"';
		$pages = paginate_links( $args );
	} else {
		/* Comments pagination */
		$total = get_comment_pages_count();
		if ( $total <= 1 ) {
			return;
		}
		$current = get_query_var( 'cpage' ) ? intval( get_query_var( 'cpage' ) ) : 1;
		$nav_atts = 'id="comments-nav" class="nav-comments" aria-label="' . esc_html__( 'Comment page navigation', 'magic-hat' ) . '"';
		$args['echo'] = false;
		$pages = paginate_comments_links( $args );
	}

	echo '<nav ' . $nav_atts . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( $current <= 1 ) {
		echo '<span aria-label="' . esc_html__( 'This is the first page.', 'magic-hat' ) . '" class="page-numbers prev page-numbers-disabled">' . $args['prev_text'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo str_replace( 'a class', 'a aria-label="' . esc_html__( 'Previous page', 'magic-hat' ) . '" class', array_shift( $pages ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/* Remove the next link from the array if it exists, since we use our own */
	if ( $total > 5 ) {
		if ( $current <= 1 || $current >= $total ) {
			$adjusted_total = 4;
		} else {
			$adjusted_total = 5;
		}
	} elseif( $total > 2 ) {
		$adjusted_total = 2;
	} else {
		if ( $current <= 1 || $current >= $total ) {
			$adjusted_total = $total - 1;
		} else {
			$adjusted_total = $total;
		}
	}

	for ( $n = 0; $n <= $adjusted_total; $n++ ) {
		if ( ($n + 1) == $current ) {
			echo str_replace( 'span aria', 'span tab-index="0" aria', $pages[$n] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo $pages[$n]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	if ( $current >= $total ) {
		echo '<span aria-label="' . esc_html__( 'This is the last page.', 'magic-hat' ) . '" class="page-numbers next page-numbers-disabled">' . $args['next_text'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} else {
		echo str_replace( 'a class', 'a aria-label="' . esc_html__( 'Next page', 'magic-hat' ) . '" class', end( $pages ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</nav>';
}
endif;

/**
 * Wrapper for {@see human_time_diff} with translatable strings to show a relative
 * timestamp, e.g., '8 hours ago,' which we can use in comments.
 *
 * @since 1.0.0
 *
 * @param int $time		The timestamp of the comment or post you want a relative
 * 						time for. Send it as a timestamp or I'll kill u
 * @return string
 */
function magic_hat_relative_time( $time ) {
	if ( ! is_int( $time ) ) {
		$time = strtotime( $time );
	}
	if ( ! $time ) {
		return;
	}
	$time_strings = array();
	$time_strings['now'] = __( 'Just now', 'magic-hat' );
	/* translators: used for relative time, e.g., "5 minutes ago" */
	$time_strings['ago'] = __( '%s ago', 'magic-hat' );

	/**
	 * Customize the relative time labels. The default array includes 'now' for time
	 * differences under a minute and 'ago' for everything else.
	 *
	 * @since 1.0.0
	 *
	 * @param array $time_strings	The array of translatable strings
	 */
	$time_strings = apply_filters( 'magic_hat_relative_time_labels', $time_strings );
	$delta = human_time_diff( $time, current_time( 'timestamp' ) );

	/* translators: wp-includes/formatting.php should have a line (3617 as of version 5.1)
	that looks like 'sprintf( _n( '%s min', '%s mins', $mins ), $mins )' -- translate the
	Magic Hat string here to the formatted singular variation, e.g., '1 min'. When WordPress
	returns this string for results of 60 seconds or less, it will get replaced with 'Just
	now'. Thanks for reading, I love you! */
	if ( strpos( $delta, __( '1 min', 'magic-hat' ) ) !== false ) {
		return esc_html( $time_strings['now'] );
	}

	$delta = str_replace( _x( 'mins', 'unit of time', 'magic-hat' ), _x( 'minutes', 'unit of time', 'magic-hat' ), $delta );
	return esc_html( sprintf( $time_strings['ago'], $delta ) );
}

if ( ! function_exists( 'magic_hat_the_posts_navigation' ) ) :
/**
 * Prints the next/previous posts navigation for single post displays with an added
 * heading above each.
 *
 * @since 1.0.0
 *
 * @param array $args {
 * 		@type string $prev_title		 The title to show above the previous post.
 * 										 Default is 'Older'.
 * 		@type string $next_title		 The title to show above the next post. Default
 * 										 is 'Newer'.
 * 		@type string $screen_reader_text The screenreader text is now an aria-label on
 * 										 the nav element instead of a hidden h2.
 * }
 */
function magic_hat_the_post_navigation( $args = array() ) {
	$args = wp_parse_args(
        $args,
        array(
			'prev_title' => esc_html__( 'Older', 'magic-hat' ),
			'next_title' => esc_html__( 'Newer', 'magic-hat' ),
            'screen_reader_text' => esc_html__( 'Post navigation', 'magic-hat' ),
        )
	);
	/**
	 * Filters the arguments for {@see magic_hat_the_post_navigation}.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args	The array of arguments.
	 * @return array		The filtered arguments.
	 */
	$args = apply_filters( 'magic_hat_the_post_navigation', $args );

    $navigation = '';

	$posts = array(
		get_adjacent_post(),
		get_adjacent_post( false, '', false )
	);

    if ( $posts ) {
		foreach ( $posts as &$post ) {
			if ( $post ) {
				$permalink = get_permalink( $post );
				/** This filter is documented in wp-includes/post-template.php */
				$title = apply_filters( 'the_title', $post->post_title, $post->ID );
				$date = mysql2date( get_option( 'date_format' ), $post->post_date );
				$post = "
				<a class=\"adjacent-item__title\" href=\"{$permalink}\">{$title}</a>
				<p class=\"adjacent-item__meta\">{$date}</p>";
			}
		}
		$previous = $posts[0];
		$next = $posts[1];

		$prev_class = $previous ? '' : ' disabled';
		$previous = $previous ?: esc_html__( 'This is the first post.', 'magic-hat' );
		$next_class = $next ? '' : ' disabled';
		$next = $next ?: esc_html__( 'This is the newest post.', 'magic-hat' );

        $navigation = '
		<nav class="nav-adjacent" aria-label="' . esc_attr( $args['screen_reader_text'] ) . '">
			<div class="nav__grid">
				<div class="adjacent-item adjacent-item-prev">
					<h3 class="adjacent-item__heading' . $prev_class . '">' . esc_html( $args['prev_title'] ) . '</h3>' .
					$previous . '
				</div>
				<div class="adjacent-item adjacent-item-next">
					<h3 class="adjacent-item__heading' . $prev_class . '">' . esc_html( $args['next_title'] ) . '</h3>' .
					$next . '
				</div>
			</div>
		</nav>';
    }

    echo $navigation; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
endif;

/**
 * Prints a list of monthly archive links for the given year.
 *
 * @param array $args {
 * 		@type string $year			  The year to get monthly archives for. Default is the
 * 									  year from the current WP_Query, as this function is
 * 									  intended to be called from yearly archive pages.
 * 		@type string $type			  How to display the monthly links. Default is 'links',
 * 									  which is plain anchor elements. Use 'list' to output
 * 									  them as an ordered list.
 * 		@type bool $full_names		  Whether to show full ("January") or abbreviated ("Jan")
 * 									  month names. Default true.
 * 		@type bool|string $hide_empty Whether to hide archive links for months without any
 * 									  posts. Default true, though you could set it to false
 * 									  if you're concerned about the extra database call. Set
 * 									  to 'disabled' to show unlinked text for empty months.
 * }
 */
function magic_hat_list_months( $args = array() ) {
	$defaults = array(
		'year' => get_query_var( 'year' ),
		'type' => 'links',
		'full_names' => true,
		'hide_empty' => true,
	);
	$args = wp_parse_args( $args, $defaults );
	/* translators: month name, year */
	$label = esc_html__( 'View all posts from:', 'magic-hat' );

	if ( $args['hide_empty'] ) {

		$posts = get_posts ( array(
			'posts_per_page' => -1,
			'post_type' => 'post',
			'date_query' => array(
				array(
					'year'  => $args['year'],
				),
			),
		) );

		$has_posts = array( 1 => array(), 2 => array(), 3 => array(),
		4 => array(), 5 => array(), 6 => array(), 7 => array(), 8 => array(),
		9 => array(), 10 => array(), 11 => array(), 12 => array() );

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$post_month = date( 'n', strtotime( $post->post_date ) );
				$has_posts[$post_month][] = 'post';
			}
		}

		wp_reset_postdata();
	}

	$output = '';
	for ( $month = 1; $month <= 12; $month++ ) {
		$link = '';
		if ( $args['hide_empty'] === true ) {
			if ( empty( $has_posts[$month] ) ) {
				continue;
			}
		} elseif ( $args['hide_empty'] === 'disabled' ) {
			if ( empty( $has_posts[$month]) ) {
				$link = '<span class="disabled" aria-hidden="true">' . date( 'F', strtotime( '2000-' . $month ) ) . '</span>';
			}
		}
		if ( empty( $link ) ) {
			$link = '<a href="' . get_month_link( $args['year'], $month ) . '"><span class="screen-reader-text">' . $label . ' </span>' . date( 'F', strtotime( '2000-' . $month ) ) . '</a>';
		}
		$link =  $args['type'] ? '<li class="archive-list-month">' . $link . '</li>' : $link;
		$output .= $link;
	}

	$output = $args['type'] === 'list' ? '<ol class="archive-list-months">' . $output . '<ol>' : $output;
	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( ! function_exists( 'magic_hat_link_pages' ) ) :
/**
 * Prints the pages for a single paginated post. Both the numbered page links and
 * next/previous links are shown. The next/previous links show a title or preview text
 * of the page they are linking to.
 *
 * @since 1.0.0
 *
 * @param array $args	{
 * 		Similar arguments array to {@see wp_link_pages}.
 * 		@type int $maxwords				Newly added: the max number of words to show in
 * 										a page preview title if the first element on that
 * 										page is not a heading element. Default is 8.
 * 		@type string $ariacurrent		What to use for the aria-current attribute. Default is
 * 										'page', also takes 'date', 'time', 'location', 'step'.
 * 		@type string $previouspagelink	Default is 'Next: %s' where %s is the page title.
 * 		@type string $nextpagelink		Default is 'Previous: %s' where %s is the page title.
 * 		@type string $pagelink			Default is '%s' where %s is the page number.
 * }
 */
function magic_hat_link_pages( $args = array() ) {
	global $post, $page, $numpages, $multipage;
	if ( ! $multipage ) {
		return;
	}

    $defaults = array(
		'maxwords' => 8,
        'aria_current' => 'page',
				/* translators: %s is the title of the previous page */
        'previouspagelink' => esc_html__( 'Previous: %s', 'magic-hat' ),
				/* translators: %s is the title of the next page. */
        'nextpagelink' => esc_html__( 'Next: %s', 'magic-hat' ),
		'pagelink' => '%',
    );

	$params = wp_parse_args( $args, $defaults );

	/** This filter is documented in wp-includes/post-template.php */
	$r = apply_filters( 'wp_link_pages_args', $params );

	/* Use the starting text of each page to grab preview titles to show in the nav */
	$titles = explode( '<!--nextpage-->', $post->post_content );
	$allowed_html = array(
		'del' => array(),
		'em' => array(),
		'span' => array(
			'class' => array(),
			'style' => array(),
			'title' => array(),
		),
		's' => array(),
		'strong' => array(),
		'u' => array(),
	);
	$r['maxwords'] = is_int( $r['maxwords'] ) ? $r['maxwords'] : 8;
	foreach ( $titles as &$title ) {
		if ( preg_match( '#<(h[1-6])#i', substr( $title, 0, 3 ), $heading ) ) {
			/* If the first thing on the new page is a heading element, use the entire
			heading since it's effectively the page title. */
			$open_tag = strpos( $title, '>' );
			$close_tag = strpos( $title, '</' . $heading[1] );
			if ( $open_tag !== false && $close_tag !== false ) {
				$open_tag++;
				$title = substr( $title, $open_tag, $close_tag - $open_tag );
				/* Allowing minimal HTML makes getting a substring way more difficult, so
				let's just strip all tags. */
				$title = wp_kses( $title, array() );
			}
		} else {
			/* Otherwise excerpt the HTML-filtered text */
			$title = wp_kses( $title, $allowed_html );
			$title = explode( ' ', $title );
			$title = implode( ' ', array_slice( $title, 0, $r['maxwords'] ) );
			$last_char = substr( $title, -1 );
			if ( ! in_array( $last_char, array( '.', '?', '\'', '"', ')', '!' ) ) ) {
				if ( in_array( $last_char, array( ' ', ',', ';' ) ) ) {
					$title = substr( $title, 0, strlen( $title ) - 1 );
				}
				/* Only add an ellipsis if the content was cut off mid-sentence */
				$title = $title . '&hellip;';
			}
		}
	}

	$output = '
	<h2 class="nav-post-title">' . esc_html__( 'Keep reading:', 'magic-hat' ) . '</h2>
	<div class="nav-post">';

	$prev = $page - 1;
	if ( $prev > 0 ) {
		$link = '<div class="post-page-prev">' . _wp_link_page( $prev ) . sprintf( $r['previouspagelink'], $titles[$page - 2] ) . '</a></div>';
		/** This filter is documented in wp-includes/post-template.php */
		$output .= apply_filters( 'wp_link_pages_link', $link, $prev );
	}
	$output .= '<div class="post-page-numbers">';
	for ( $i = 1; $i <= $numpages; $i++ ) {
		$link = str_replace( '%', $i, $r['pagelink'] );
		/* add '|| ! $more && 1 == $page' to link the first page if you're showing page links
		in an archive view. Remember to declare global $more, too */
		if ( $i != $page ) {
			$link = _wp_link_page( $i ) . $link . '</a>';
			$link = str_replace( 'post-page-numbers', 'post-page-number', $link );
		} elseif ( $i === $page ) {
			$link = '<span class="post-page-current current" aria-current="' . esc_attr( $r['aria_current'] ) . '">' . $link . '</span>';
		}
		/** This filter is documented in wp-includes/post-template.php */
		$output .= apply_filters( 'wp_link_pages_link', $link, $i );
	}
	$output .= '</div>';
	/* use 'if ( $more )' to suppress this on posts excerpted by a <!-- more --> tag
	if you choose to show page links in archive views */
	$next = $page + 1;
	if ( $next <= $numpages ) {
		if ( $prev ) {
			$output .= $r['separator'];
		}
		$link = '<div class="post-page-next">' . _wp_link_page( $next ) . sprintf( $r['nextpagelink'], $titles[$page] ) . '</a></div>';

		/** This filter is documented in wp-includes/post-template.php */
		$output .= apply_filters( 'wp_link_pages_link', $link, $next );
	}
	$output .= '</div><!-- .nav-post -->';
	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
endif;
