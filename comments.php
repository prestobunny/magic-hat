<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current
 * comments page and the comment form.
 *
 * @package Magic Hat
 * @since 1.0.0
 * @todo Webmention support?
 */

/* Hide comments if a password is still required */
if ( post_password_required() ) {
	return;
}
?>

<?php
$magic_hat_ping_number = get_comments( array(
    'post_id' => $post->ID,
    'count' => true,
    'type' => 'pings',
) );
if ( $magic_hat_ping_number ) {
?>
	<section id="pings" class="comments-area">
		<h2 id="pings-title" class="pings-title"><?php echo esc_html_x( 'Mentions', 'plural noun', 'magic-hat' ); ?></h2>
		<p id="pings-status" class="comments__status comments__status-success"></p>
		<ol class="comments__list">
			<?php
			wp_list_comments( array(
				'callback' => 'magic_hat_comment_callback',
				'style' => 'ol',
				'type' => 'pings',
				'short_ping' => false
			) );
			?>
		</ol>
	</section>
<?php } ?>

<?php
if ( get_option( 'page_comments' ) ) {
	global $wp_query;
	$magic_hat_current_cpage = get_query_var( 'cpage' ) ? intval( get_query_var( 'cpage' ) ) : 1;
	$magic_hat_current_cpage = sprintf(
		/* translators: Showing page {current} of {total} */
		esc_html__( 'Showing page %1$d of %2$d', 'magic-hat' ), $magic_hat_current_cpage, get_comment_pages_count()
	);
} else {
	$magic_hat_current_cpage = '';
}
?>
<section id="comments" class="comments">
	<h2 id="comments-title" class="comments__title"><?php echo esc_html( get_theme_mod( 'comments-title', esc_html__( 'Comments', 'magic-hat' ) ) ); ?></h2>
	<p id="comments-status" class="comments__status comments__status-success"></p>
	<?php if ( have_comments() ) { ?>
		<div id="comments-filler"></div>
		<div id="comments-list-nav">
			<p id="comments-page-title" class="comments__title-cpage"><?php echo $magic_hat_current_cpage; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<ol id="comments-list" class="comments__list" <?php echo magic_hat_get_overflow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php
				wp_list_comments( array(
					'callback' => 'magic_hat_comment_callback',
					'style' => 'ol',
					'type' => 'comment',
				) );
				?>
			</ol><!-- .comment-list -->

			<?php
			magic_hat_paginate_links( array(
				'prev_text' => __( '&lsaquo;', 'magic-hat' ),
				'next_text' => __( '&rsaquo;', 'magic-hat' ),
				), 'comments'
			);
			?>
		</div>
		<?php
	} else {
		echo '<p class="comments__status comments__status-none">' . esc_html__( 'There are no comments yet. Be the first!', 'magic-hat' ) . '</p>';
	} // endif have_comments()

	if ( comments_open() ) {
		$args = magic_hat_comment_labels();
		comment_form( $args );
	} else {
		echo '<p class="comments__status comments__status-closed">' . esc_html__( 'Comments are closed.', 'magic-hat' ) . '</p>';
	}
	?>
</section><!-- #comments -->
