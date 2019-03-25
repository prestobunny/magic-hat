<?php
/**
 * Sidebar
 *
 * The template for showing the main page body sidebar.
 *
 * @package Magic Hat
 * @since 1.0.0
 */

$magic_hat_sidebar = get_theme_mod( 'sidebar-side', 'sidebar-right' );
if ( ! is_active_sidebar( 'sidebar' ) || empty( $magic_hat_sidebar ) ) {
	return;
}
?>

<aside id="secondary" class="content-secondary sidebar <?php echo esc_attr( $magic_hat_sidebar ); ?>">
	<?php dynamic_sidebar( 'sidebar' ); ?>
</aside><!-- #secondary -->
