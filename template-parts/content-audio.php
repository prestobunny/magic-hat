<?php
/**
 * Audio Post Content
 *
 * Template part for displaying audio posts.
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

$magic_hat_id = get_the_ID();
?>

<?php magic_hat_entry_header( false, $magic_hat_id ); ?>

<?php if ( ! post_password_required() ) { ?>
	<div class="entry-embed-rich">
		<?php
		$magic_hat_content_type = rwmb_meta( '_format_audio_type' );
		switch ( $magic_hat_content_type ) {
			case 'upload' :
				$magic_hat_content = rwmb_meta( '_format_audio_file' );
				$magic_hat_content = reset( $magic_hat_content );
				$magic_hat_artist = ! empty( $magic_hat_content['artist'] ) ? esc_attr( $magic_hat_content['artist'] ) : esc_html__( 'Unknown', 'magic-hat' );
				$magic_hat_album = ! empty( $magic_hat_content['album'] ) ? esc_attr( $magic_hat_content['album'] ) : esc_html__( 'Unknown', 'magic-hat' );
				?>
				<figure class="entry-embed-audio">
					<?php the_post_thumbnail( 'thumbnail' ); ?>
					<div class="audio-column-right">
						<figcaption class="audio-data">
							<p class="audio-data-artist"><?php
							/* translators: %s is the name of the song/audio file artist. */
							printf( wp_kses_post( __( '<strong>Artist:</strong> %s', 'magic-hat' ) ), $magic_hat_artist ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
							<p class="audio-data-album"><?php
							/* translators: %s isthe name of the song/audio file album */
							printf( wp_kses_post( __( '<strong>Album:</strong> %s', 'magic-hat' ) ), $magic_hat_album ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						</figcaption>
						<?php echo wp_audio_shortcode( array( 'src' => esc_url( $magic_hat_content['url'] ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div><!-- .audio-column-right -->
				</figure>
				<?php
				break;
			case 'embed' :
				echo rwmb_meta( '_format_audio_embed' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'shortcode' :
				$magic_hat_content = rwmb_meta( '_format_audio_shortcode' );
				echo do_shortcode( $magic_hat_content );
				break;
		} ?>
	</div>
<?php } ?>

<div class="entry__content">
	<?php magic_hat_content_excerpt(); ?>
</div><!-- .entry__content -->

<?php if ( is_singular() ) { ?>
	<footer class="entry__meta entry__meta-footer">
		<?php magic_hat_entry_footer(); ?>
	</footer><!-- .entry__meta-footer -->
<?php } ?>
