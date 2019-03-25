<?php
/**
 * Link Post Content
 *
 * Template for showing link posts.
 *
 * @package Magic Hat
 * @subpackage Post Formats
 * @since 1.0.0
 */

$magic_hat_tag = is_singular() ? 'h1' : 'h2';
$magic_hat_url = esc_url( rwmb_meta( '_format_link_url' ) );
?>

<?php magic_hat_post_thumbnail(); ?>

<header class="entry__header">
	<?php the_title( '<' . $magic_hat_tag . ' class="entry__title"><a href="' . $magic_hat_url . '">', '</a></' . $magic_hat_tag . '>' ); ?>
	<div class="entry__meta entry__meta-header">
		<?php echo magic_hat_get_entry_meta(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div><!-- .entry__meta-header -->
</header><!-- .entry__header -->

<div class="entry__content">
	<?php magic_hat_content_excerpt(); ?>
</div><!-- .entry__content -->

<?php if ( is_singular() ) { ?>
	<footer class="entry__meta entry__meta-footer">
		<?php magic_hat_entry_footer(); ?>
	</footer><!-- .entry__meta-footer -->
<?php } ?>
