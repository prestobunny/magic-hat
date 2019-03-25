<?php
/**
 * Media Functions
 * 
 * Functions used to control the display of media and embedded content.
 * 
 * @package Magic Hat
 * @subpackage Includes
 * @since 1.0.0
 */

if ( ! function_exists( 'magic_hat_enqueue_media_scripts' ) ) :
/**
 * Registers, localizes, and enqueues scripts and styles related to lightboxes and other
 * media functionality.
 * 
 * @since 1.0.0
 */
function magic_hat_enqueue_media_scripts() {
	wp_register_script( 'swipebox', get_template_directory_uri() . '/assets/js/swipebox.min.js', array('jquery'), null, true );
	if ( get_theme_mod( 'use-lightbox', true ) && is_singular() ) {
		wp_localize_script( 'swipebox', 'magic_hat', array(
			'title' => esc_html__( 'Image lightbox', 'magic-hat' ),
			'loading' => esc_html__( 'Loading', 'magic-hat' ),
			'close' => esc_html__( 'Close lightbox', 'magic-hat' ),
			'next' => esc_html__( 'Next image', 'magic-hat' ),
			'previous' => esc_html__( 'Previous image', 'magic-hat' ),
		) );
		wp_enqueue_script( 'swipebox' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'magic_hat_enqueue_media_scripts' );


if ( ! function_exists( 'magic_hat_calculate_image_sizes' ) ) :
/**
 * Generates a value for an image's 'sizes' attribute based on the size of the image, which
 * is determined by the array of size names passed to the function.
 * 
 * @param array $output			The value of the attribute which gets filtered here.
 * @param string|array $size 	The max image size, either an array (width, height) or the
 * 								name of the size as a string.
 * @return string				Filtered value for use in an image's 'sizes' attribute.
 */
function magic_hat_calculate_image_sizes( $output, $size ) {
	$medium = '(max-width: 400px) 300px';
	if ( is_page_template( 'page-fullwidth.php') || get_theme_mod( 'sidebar-side' ) === 'no-sidebar' ) {
		/* Width is consistent because there's no sidebar. */
		$medium_large = '(min-width: 401px) 768px';
		$large = '(min-width: 930px) 1024px';
	} else {
		/* Adjust width based on sidebar placement (on small screens it goes underneath) */
		$medium_large = '(min-width: 401px) and (max-width: 870px) 768px';
		$large = '(min-width: 930px) and (max-width: 1250px) 768px, (min-width: 871px) and (max-width: 929px) 1024px, 1024px';
	}
	
	if ( is_array( $size ) ) {
		$size = absint( $size[0] );
	}

	switch ( true ) {
		case $size = 'large' || $size >= 1024 :
			$output = "{$medium}, {$medium_large}, {$large}";
			break;
		case $size = 'medium_large' || $size >= 768 :
			$output = "{$medium}, {$medium_large}";
			break;
		case $size = 'medium' || $size >= 300 :
			$output = $medium;
			break;
	}

	return $output;
}
endif;
add_filter( 'wp_calculate_image_sizes', 'magic_hat_calculate_image_sizes', 10, 2 );

/**
 * Adds the classes 'entry-embed-{$provider}' and 'entry-embed-{$type}' to oEmbed containers.
 * This function is only run the first time the content is fetched, so it won't apply to any
 * cached oEmbed content.
 * 
 * @since 1.0.0
 * 
 * @param string $return    The HTML for the embedded element.
 * @param object $data      The oEmbed JSON/XML response body object.
 * @return string           The HTML with the new classes.
 */
function magic_hat_oembed_parse( $return, $data ) {
	if ( is_object( $data ) ) {
		$classes = array();
		if ( ! empty( $data->type ) ) {
			$classes[] = 'entry-embed-' . esc_attr( strtolower( $data->type ) );
		}
		if ( ! empty( $data->provider_name ) ) {
			$classes[] = 'entry-embed-' . esc_attr( strtolower( $data->provider_name ) );
		}
		$return = '<div class="' . implode( ' ', $classes ) . '">' . $return . '</div>';
	}
	return $return;
}
add_filter( 'oembed_dataparse', 'magic_hat_oembed_parse', 0, 2 );

/**
 * Adds a generic 'entry-embed' class to embedded content containers that don't already
 * have a class that matches the 'entry-embed-{$provider}'/'entry-embed-{$type}' format.
 * 
 * @since 1.0.0
 * 
 * @param string $cache     The cached HTML for the embedded content.
 * @return string           The cached HTML, possibly wrapped in a div with the new class.
 */
function magic_hat_oembed_wrap( $cache ) {
	if ( strpos( $cache, 'class="entry-embed' ) === false ) {
		$cache = '<div class="entry-embed">' . $cache . '</div>';
	}
	return $cache;
}
add_filter( 'embed_oembed_html', 'magic_hat_oembed_wrap' );

/**
 * Returns HTML5 markup for a gallery.
 * 
 * @since 1.0.0
 * 
 * @param array $images {
 * 		The array of images to use, which should include:
 * 		@type int $ID			Image attachment ID. The key is capitalized to match Meta
 * 								Box's formatting.
 * 		@type string $url 		Image source URL.
 * 		@type int $width		Width of the image in pixels.
 * 		@type int $height		Height of the image in pixels.
 * 		@type string $caption	Image caption.
 * 		@type string $alt		Image alt text.
 * }
 * @param array $atts {
 * 		Arguments for the gallery as a whole.
 * 		@type int|string $id	Gallery ID.
 * 		@type string $class 	List of classes to add to the gallery, separated by a space.
 * 								'gallery' is added to the start of the list automatically.
 * 		@type string $link		What the images should link to. Accepts 'file', 'none', and an
 * 								empty string if you want to link to attachment pages. That's
 * 								WP's genius idea, by the way, not mine.
 * }
 * @return string	            The gallery HTML markup.
 */
function magic_hat_get_gallery( $images, $atts ) {
	$defaults = array(
		'id' => '',
		'class' => 'gallery-col-3',
		'link' => '',
	);
	$attss = wp_parse_args( $atts, $defaults );

	$lightbox = '';
	switch ( $atts['link'] ) {
		case 'file' :
			/* file, which means the image url */
			$lightbox = 'data-lightbox';
			$link = 'file';
			break;
		case '' :
			/* empty, which means link to attachment page */
			$link = 'page';
			break;
		default:
			/* "none" or some other value */
			$link = false;
	}

	$atts['class'] = 'class="gallery ' . $atts['class'] . '"';
	$atts['id'] = empty( $atts['id'] ) ? '' : 'id="gallery-' . esc_attr( $atts['id'] ) . '"';
	$output = "
	<figure {$atts['class']} {$lightbox} {$atts['id']} role=\"group\">";
		foreach ( $images as $image ) {
			$show_caption = $link != 'file' && ! empty( $image['caption'] );
			$aria_desc = $show_caption ? "aria-describedby=\"gallery-caption-{$image['ID']}\"" : '';
			$caption = $show_caption ? "<figcaption id=\"gallery-caption-{$image['ID']}\" class=\"gallery-item-caption\">{$image['caption']}</figcaption>" : '';

			$figure = "
			<figure class=\"gallery-item\" id=\"gallery-item-{$image['ID']}\">
				<img class=\"gallery-item-image\" src=\"{$image['url']}\" width=\"{$image['width']}\" height=\"{$image['height']}\" alt=\"{$image['alt']}\" {$aria_desc}>
				{$caption}
			</figure>";

			if ( $link ) {
				$image['link'] = $link == 'file' ? $image['url'] : get_attachment_link( $image['ID'] );
				$output .= "<a class=\"gallery-linked-item\" title=\"{$image['caption']}\" href=\"{$image['link']}\">" . $figure . '</a>';
			} else {
				$output .= $figure;
			}
		}
	$output .= '
	</figure><!-- .gallery -->';
	return $output;
}

/**
 * Directs the native {@see gallery_shortcode} through {@see magic_hat_get_gallery} so that
 * native both native WordPress galleries and those created using post format meta have the
 * same format.
 * 
 * A photography theme might benefit from adding {@see wp_get_attachment_metadata}, which
 * returns detailed image metadata such as shutter speed, aperture, ISO, etc.
 * 
 * @since 1.0.0
 * 
 * @param string $output    Gallery shortcode output.
 * @param array $atts       Gallery shortcode attributes.
 * @return string	        The new HTML gallery output.
 */
function magic_hat_post_gallery( $output, $atts ) {
	$post = get_post();
	$atts  = shortcode_atts(
		array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'columns'    => 2,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => '',
		),
		$atts,
		'gallery'
	);

	if ( ! empty( $atts['include'] ) ) {
		/* User selected these images specifically, probably from the UI */
		$_attachments = get_posts( array(
			'include'        => $atts['include'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} else {
		/* Shortcode is hand-crafted and the user wants all attachments of given post,
		with possible exclusions */
		$attachments = get_children( array(
			'post_parent'    => intval( $atts['id'] ),
			'exclude'        => $atts['exclude'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => $atts['order'],
			'orderby'        => $atts['orderby'],
		) );
	}
	/* Return nothing if there are no attachments */
	if ( empty( $attachments ) ) { return ''; }

	$size_class  = sanitize_html_class( $atts['size'] );
	$atts['columns'] = intval( $atts['columns'] );
	$images = array();
	foreach ( $attachments as $id => $attachment ) {
		$src = wp_get_attachment_image_src( $id, $atts['size'], false);
		$src = $src ?: array( '', 0, 0, false );

		$images[] = array(
			'url' => $src[0],
			'width' => $src[1],
			'height' => $src[2],
			'caption' => wptexturize( $attachment->post_excerpt ),
			'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ),
			'ID' => $id,
		);
	}
	return magic_hat_get_gallery(
		$images,
		array(
			'id' => $id,
			'class' => "gallery-col-{$atts['columns']} gallery-size-{$size_class}",
			'link' => $atts['link'],
		)
	);
}
add_filter( 'post_gallery', 'magic_hat_post_gallery', 10, 2 );

/**
 * Adds a title and rel attribute to anchor elements if an image is linked to its source file.
 * This is so {@see magic_hat_caption_shortcode} can turn it into a lightbox as appropriate.
 * {@see image_send_to_editor} for a description of the parameters.
 * 
 * @since 1.0.0
 * 
 * @return string	Image tag markup, possibly wrapped in a link.
 */
function magic_hat_image_send_to_editor( $html, $id, $caption, $title, $align, $url, $size, $alt ) {
	if ( strpos( $html, "href=\"{$url}\"" ) !== false ) {
		$caption = ! empty( $caption ) ? esc_attr( $caption ) : esc_attr( $alt );
		$html = str_replace( '<a', "<a title=\"{$caption}\" rel=\"{$id}\"", $html );
	}
	return $html;
}
add_filter( 'image_send_to_editor', 'magic_hat_image_send_to_editor', 10, 8 );

/**
 * @see img_caption_shortcode
 */
function magic_hat_caption_shortcode( $output, $attr, $content ) {
	$atts = shortcode_atts(
        array(
            'id'         => '',
            'caption_id' => '',
            'align'      => 'alignnone',
            'caption'    => '',
            'class'      => '',
        ),
        $attr,
        'caption'
	);
	
    if ( empty( $atts['caption'] ) ) {
        return $content;
	}
 
    $id = $caption_id = $describedby = '';
 
    if ( $atts['id'] ) {
        $atts['id'] = esc_attr( sanitize_html_class( $atts['id'] ) );
        $id = "id=\"{$atts['id']}\" ";
    }
 
    if ( $atts['caption_id'] ) {
        $atts['caption_id'] = sanitize_html_class( $atts['caption_id'] );
    } elseif ( $atts['id'] ) {
        $atts['caption_id'] = 'caption-' . str_replace( '_', '-', $atts['id'] );
    }
 
    if ( $atts['caption_id'] ) {
        $caption_id  = 'id="' . esc_attr( $atts['caption_id'] ) . '" ';
        $describedby = 'aria-describedby="' . esc_attr( $atts['caption_id'] ) . '" ';
    }
 
	$class = 'class="' . esc_attr( trim( $atts['align'] . ' ' . $atts['class'] ) ) . '"';
	$content = do_shortcode( $content );
	$lightbox = strpos( $content, 'rel="' ) !== false ? 'data-lightbox ' : '';
	if ( strpos( $content, 'rel="' ) !== false ) {
	}

	$html = "
	<figure {$lightbox}{$id}{$describedby}{$class}>
		{$content}
		<figcaption {$caption_id}>{$atts['caption']}</figcaption>
	</figure>";
 
    return $html;
}
add_filter( 'img_caption_shortcode', 'magic_hat_caption_shortcode', 10, 3 );

function magic_hat_playlist_shortcode( $output, $attr, $instance ) {
	global $post;
	$atts = shortcode_atts(
		array(
			'type'         => 'audio',
			'order'        => 'ASC',
			'orderby'      => 'menu_order ID',
			'id'           => $post ? $post->ID : 0,
			'include'      => '',
			'exclude'      => '',
			'style'        => 'light',
			'tracklist'    => true,
			'tracknumbers' => true,
			'images'       => true,
			'artists'      => true,
		),
		$attr,
		'playlist'
	);
	$id = intval( $atts['id'] );
	if ( $atts['type'] !== 'audio' ) {
		$atts['type'] = 'video';
	}
	$args = array(
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => $atts['type'],
		'order'          => $atts['order'],
		'orderby'        => $atts['orderby'],
	);
	if ( ! empty( $atts['include'] ) ) {
		$args['include'] = $atts['include'];
		$_attachments    = get_posts( $args );
		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} elseif ( ! empty( $atts['exclude'] ) ) {
		$args['post_parent'] = $id;
		$args['exclude']     = $atts['exclude'];
		$attachments         = get_children( $args );
	} else {
		$args['post_parent'] = $id;
		$attachments         = get_children( $args );
	}
	if ( empty( $attachments ) ) {
		return '';
	}
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) {
			$output .= wp_get_attachment_link( $att_id ) . "\n";
		}
		return $output;
	}
	$outer = 22; // default padding and border of wrapper
	$default_width  = 640;
	$default_height = 360;
	$theme_width  = empty( $content_width ) ? $default_width : ( $content_width - $outer );
	$theme_height = empty( $content_width ) ? $default_height : round( ( $default_height * $theme_width ) / $default_width );
	$data = array(
		'type'         => $atts['type'],
		// don't pass strings to JSON, will be truthy in JS
		'tracklist'    => wp_validate_boolean( $atts['tracklist'] ),
		'tracknumbers' => wp_validate_boolean( $atts['tracknumbers'] ),
		'images'       => wp_validate_boolean( $atts['images'] ),
		'artists'      => wp_validate_boolean( $atts['artists'] ),
	);
	$tracks = array();
	foreach ( $attachments as $attachment ) {
		$url   = wp_get_attachment_url( $attachment->ID );
		$ftype = wp_check_filetype( $url, wp_get_mime_types() );
		$track = array(
			'src'         => $url,
			'type'        => $ftype['type'],
			'title'       => $attachment->post_title,
			'caption'     => $attachment->post_excerpt,
			'description' => $attachment->post_content,
		);
		$track['meta'] = array();
		$meta          = wp_get_attachment_metadata( $attachment->ID );
		if ( ! empty( $meta ) ) {
			foreach ( wp_get_attachment_id3_keys( $attachment ) as $key => $label ) {
				if ( ! empty( $meta[ $key ] ) ) {
					$track['meta'][ $key ] = $meta[ $key ];
				}
			}
			if ( 'video' === $atts['type'] ) {
				if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
					$width        = $meta['width'];
					$height       = $meta['height'];
					$theme_height = round( ( $height * $theme_width ) / $width );
				} else {
					$width  = $default_width;
					$height = $default_height;
				}
				$track['dimensions'] = array(
					'original' => compact( 'width', 'height' ),
					'resized'  => array(
						'width'  => $theme_width,
						'height' => $theme_height,
					),
				);
			}
		}
		if ( $atts['images'] ) {
			$thumb_id = get_post_thumbnail_id( $attachment->ID );
			if ( ! empty( $thumb_id ) ) {
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'full' );
				$track['image']               = compact( 'src', 'width', 'height' );
				list( $src, $width, $height ) = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
				$track['thumb']               = compact( 'src', 'width', 'height' );
			} else {
				$src            = get_template_directory_uri() . '/assets/img/blank-album.png';
				$width          = 150;
				$height         = 150;
				$track['image'] = compact( 'src', 'width', 'height' );
				$track['thumb'] = compact( 'src', 'width', 'height' );
			}
		}
		$tracks[] = $track;
	}
	$data['tracks'] = $tracks;
	$safe_type  = esc_attr( $atts['type'] );
	$safe_style = esc_attr( $atts['style'] );
	ob_start();
	if ( 1 === $instance ) {
		/**
		 * Prints and enqueues playlist scripts, styles, and JavaScript templates.
		 *
		 * @since 3.9.0
		 *
		 * @param string $type  Type of playlist. Possible values are 'audio' or 'video'.
		 * @param string $style The 'theme' for the playlist. Core provides 'light' and 'dark'.
		 */
		do_action( 'wp_playlist_scripts', $atts['type'], $atts['style'] );
	}
	?>
<figure class="entry-embed-audio wp-playlist wp-<?php echo $safe_type; ?>-playlist wp-playlist-<?php echo $safe_style; ?>">
	<div class="audio-column-right">
		<?php if ( 'audio' === $atts['type'] ) : ?>
		<div class="wp-playlist-current-item"></div>
		<?php endif ?>
		<<?php echo $safe_type; ?> controls="controls" preload="none" width="
					<?php
					echo (int) $theme_width;
					?>
		"
		<?php
		if ( 'video' === $safe_type ) :
			echo ' height="', (int) $theme_height, '"';
		endif;
		?>
		></<?php echo $safe_type; ?>>
		<div class="wp-playlist-next"></div>
		<div class="wp-playlist-prev"></div>
	</div>
	<noscript>
	<ol>
	<?php
	foreach ( $attachments as $att_id => $attachment ) {
		printf( '<li>%s</li>', wp_get_attachment_link( $att_id ) );
	}
	?>
	</ol>
	</noscript>
	<script type="application/json" class="wp-playlist-script"><?php echo wp_json_encode( $data ); ?></script>
</figure>
	<?php
	return ob_get_clean();
}
add_filter( 'post_playlist', 'magic_hat_playlist_shortcode', 10, 3 );