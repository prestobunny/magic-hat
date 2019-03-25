<?php
/**
 * Default Template
 *
 * The main template file that gets called when nothing more specific matches the query.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

get_header();
?>

	<?php if ( is_home() && ! is_front_page() ) { ?>
		<header>
			<h1 class="page__title screen-reader-text"><?php single_post_title(); ?></h1>
		</header>
	<?php } ?>
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
		<p><?php esc_html_e( 'Nothing here yet.', 'magic-hat' ); ?></p>

		<?php if ( current_user_can( 'edit-posts' ) ) { ?>
			<a href="<?php echo esc_url( admin_url('post-new.php') ); ?>"><?php esc_html_e( 'Create your first post.' ); ?></a>
		<?php } // endif current_user_can( 'edit-posts' ) ?>

	<?php } // endif have_posts() ?>
</main><!-- primary -->

<?php
get_sidebar();
get_footer();
