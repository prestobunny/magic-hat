<?php
/**
 * Archive Template
 *
 * The template for displaying category, tag, post-type, author, and date-based archives.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
?>

	<header class="page__header">
		<h1 class="page__title"><?php echo esc_html( get_the_archive_title() ); ?></h1>
		<div class="page__desc"><?php echo wp_kses_post( get_the_archive_description() ); ?></div>
		<?php if ( is_year() ) { ?>
			<nav class="nav-menu nav-desktop-h" aria-label="<?php esc_html_e( 'Browse posts by month' ); ?>">
				<?php magic_hat_list_months( array( 'type' => 'list', 'hide_empty' => 'disabled' ) ); ?>
			</nav>
		<?php } ?>
	</header><!-- .page__header -->

	<?php if ( have_posts() ) { ?>
		<?php while ( have_posts() ) { ?>
			<?php the_post(); ?>

			<article id="post-<?php echo get_the_ID(); ?>" <?php post_class(); ?>>
				<?php magic_hat_sticky_ribbon(); ?>
				<?php magic_hat_get_post_template(); ?>
			</article>
		<?php } ?>

		<?php magic_hat_paginate_links( array(), 'posts' ); ?>

	<?php } else { ?>
		<p><?php esc_html_e( 'No posts matched your selection.', 'magic-hat' ); ?></p>
	<?php } // endif have_posts() ?>

</main><!-- primary -->

<?php
get_sidebar();
get_footer();
