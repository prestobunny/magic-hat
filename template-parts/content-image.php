<?php
/**
 * Image Post Content
 *
 * Template part for displaying image posts.
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

global $post;
$magic_hat_id = get_the_ID();
$magic_hat_describedby = empty( $post->post_content ) ? '' : ' aria-describedby="entry__content-' . $magic_hat_id . '"';
?>

<?php magic_hat_entry_header( false, $magic_hat_id ); ?>

<?php if ( ! post_password_required() ) { ?>
	<figure class="entry-embed-photo" <?php echo $magic_hat_describedby; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php
		$magic_hat_content_type = rwmb_meta( '_format_image_type' );
		switch ( $magic_hat_content_type ) {
			case 'upload' :
				$magic_hat_content = rwmb_meta( '_format_image_file', array( 'size' => 'full' ) );
				$magic_hat_content = reset( $magic_hat_content );
				echo '
				<img src="' . esc_url( $magic_hat_content['url'] ) . '"
					srcset="' . esc_attr( $magic_hat_content['srcset'] ) . '"
					sizes="' . esc_attr( wp_calculate_image_sizes( array( $magic_hat_content['width'], $magic_hat_content['height'] ) ) ) . '"
					width="' . esc_attr( $magic_hat_content['width'] ) . '"
					height="' . esc_attr( $magic_hat_content['height'] ) . '"
					alt="' . esc_attr( $magic_hat_content['alt'] ) . '">';
				$magic_hat_content_caption = ! empty( $magic_hat_content['caption'] ) ? $magic_hat_content['caption'] : $magic_hat_content['description'];
				if ( ! empty( $magic_hat_content_caption ) ) {
					?>
					<figcaption><?php echo esc_html( $magic_hat_content_caption ); ?></figcaption>
					<?php
				}
				break;
			case 'embed' :
				echo rwmb_meta( '_format_image_embed' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'shortcode' :
				$magic_hat_content = rwmb_meta( '_format_image_shortcode' );
				echo do_shortcode( $magic_hat_content );
				break;
		} ?>
	</figure>
<?php } ?>

<div id="entry__content-<?php echo esc_attr( $magic_hat_id ); ?>" class="entry__content">
	<?php magic_hat_content_excerpt(); ?>
</div><!-- .entry__content -->

<?php if ( is_singular() ) { ?>
	<footer class="entry__meta entry__meta-footer">
		<?php magic_hat_entry_footer(); ?>
	</footer><!-- .entry__meta-footer -->
<?php } ?>
