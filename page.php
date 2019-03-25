<?php
/**
 * Page Template
 *
 * This is the template that displays all non-post pages by default.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
?>

	<?php while ( have_posts() ) { ?>
		<?php the_post(); ?>
		<article <?php post_class(); ?>>
			<?php get_template_part( 'template-parts/content' ); ?>
		</article>

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
