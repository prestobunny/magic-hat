<?php
/**
 * 404 Template
 *
 * The template for displaying the 404 error page.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
?>

	<header class="page__header">
		<h1 class="page__title page__title-error"><?php echo esc_html( get_theme_mod( '404-title', '404' ) ); ?></h1>
	</header><!-- .page__header -->
	<p class="page__desc"><?php esc_html_e( 'This page has gone incognito.', 'magic-hat' ); ?></p>
	<?php echo get_search_form(); ?>
</main><!-- primary -->

<?php
get_footer();
