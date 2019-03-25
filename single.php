<?php
/**
 * Single Post
 *
 * The template for displaying single posts.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
?>

	<?php while ( have_posts() ) { ?>
		<?php the_post(); ?>

		<article id="post-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
			<?php magic_hat_get_post_template(); ?>
		</article>

		<?php
		if ( get_theme_mod( 'show-nextprev-posts', true ) ) {
			magic_hat_the_post_navigation();
		}
		?>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>

	<?php } ?>
</main><!-- primary -->

<?php
get_sidebar();
get_footer();
