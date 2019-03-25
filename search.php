<?php
/**
 * Search Results
 *
 * The template for displaying search results pages.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
global $wp_query;
?>

	<?php if ( have_posts() ) { ?>
		<header class="page__header">
			<h1 class="page__title">
				<?php printf(
					/* translators: Showing {results number} for {search term} */
					esc_html__( 'Showing %1$s results for "%2$s"', 'magic-hat' ),
					'<span class="search-results">' . esc_html( $wp_query->found_posts ) . '</span>',
					'<span class="search-query">' . get_search_query() . '</span>'
				); ?>
			</h1>
		</header><!-- .page__header -->

		<?php get_search_form(); ?>
		<?php while ( have_posts() ) { ?>
			<?php the_post(); ?>

			<article id="post-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
				<?php magic_hat_get_post_template(); ?>
			</article>
		<?php } ?>

		<?php magic_hat_paginate_links( array(), 'posts' ); ?>

	<?php } else { ?>
		<header class="page__header">
			<h1 class="page__title">
				<?php printf(
					/* translators: %s is the search term. */
					esc_html__( 'No results found for "%s"', 'magic-hat' ),
					'<span class="search-results">' . esc_html( $wp_query->found_posts ) . '</span>'
				); ?>
			</h1>
		</header><!-- .page__header -->
		<p><?php
		/* translators: %s is for the wrapping anchor tags. There's a period outside of the anchor element. */
		printf( esc_html__( 'Try another search or %1$sbrowse the archives%2$s.', 'magic-hat' ), '<a href="' . esc_url( get_post_type_archive_link( 'post' ) ) . '">', '</a>' ); ?></p>
	<?php } // endif have_posts() ?>

</main><!-- primary -->

<?php
get_sidebar();
get_footer();
