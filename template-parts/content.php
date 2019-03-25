<?php
/**
 * Post Content
 * 
 * Template part for displaying standard posts. {@see wp_link_pages} is called from
 * {@see magic_hat_content_excerpt}, so it doesn't need to be added here. Please note
 * that the wrapping article element is coded in the template files that call content
 * template parts (index, single, archive, etc.) so it is not necessary here.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

?>

<?php magic_hat_post_thumbnail(); ?>

<?php magic_hat_entry_header(); ?>

<div class="entry__content">
	<?php magic_hat_content_excerpt(); ?>
</div><!-- .entry__content -->

<?php if ( is_singular() ) { ?>
	<footer class="entry__meta entry__meta-footer">
		<?php magic_hat_entry_footer(); ?>
	</footer><!-- .entry__meta-footer -->
<?php } ?>
