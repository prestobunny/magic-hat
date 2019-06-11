<?php
/**
 * Header Template
 *
 * Template that shows the site markup up until the opening .site__content tag
 * and the breadcrumb trail that comes after it.
 *
 * If you want to remove the main menu, you should also edit navigation.js to
 * remove the related script (i.e., for submenu keyboard accessibility and
 * toggling the menu on mobile). Don't dequeue the file completely, as it
 * contains script to handle tab focus for "skip content" links.
 *
 * @package Magic Hat
 * @since 1.0.0f
 */

$magic_hat_body_class = array(
	get_theme_mod( 'use-boxed', false ) ? 'boxed' : '',
	get_theme_mod( 'use-underline', 0 ) === 0 ? '' : 'accessible-links',
);
$magic_hat_header_style = has_header_image() ? 'style="background-image:url(\'' . get_header_image() . '\');"' : '';
$magic_hat_title_class = display_header_text() ? '' : 'screen-reader-text';
$magic_hat_description = get_bloginfo( 'description', 'display' );
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta description="<?php bloginfo( 'description' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $magic_hat_body_class ); ?> <?php if ( function_exists( 'magic_hat_custom_bg' ) ) magic_hat_custom_bg(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'magic-hat' ); ?></a>

	<header id="masthead" class="site__header">
		<div class="header-bg" <?php echo $magic_hat_header_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( has_nav_menu( 'menu-top' ) ) { ?>
				<nav aria-label="<?php esc_html_e( 'Topbar navigation', 'magic-hat' ); ?>" id="nav-top" class="nav-top">
					<?php wp_nav_menu( array(
						'theme_location' => 'menu-top',
						'container' => 'ul',
						'depth' => 1,
						'menu_class' =>
						'nav__list nav__list-h'
					) ); ?>
				</nav><!-- #nav-top -->
			<?php } ?>

			<div class="header__banner">
				<div class="banner__branding">
					<?php the_custom_logo(); ?>

					<div class="branding__title <?php echo $magic_hat_title_class; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
						<?php if ( is_front_page() && is_home() ) { ?>
							<h1 class="site__title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<?php } else { ?>
							<p class="site__title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
						<?php } ?>

						<?php if ( $magic_hat_description || is_customize_preview() ) { ?>
							<p class="site__desc"><?php echo $magic_hat_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
						<?php } ?>
					</div>
				</div><!-- .banner-branding -->

				<div class="banner__widget">
					<?php dynamic_sidebar( 'header' ); ?>
				</div><!-- .banner-widget -->
			</div><!-- header__banner -->
		</div><!-- header-bg -->

		<?php if ( has_nav_menu( 'menu-main' ) ) { ?>
			<nav id="nav-main" class="nav-main">
				<button id="nav-main__toggle" class="nav-main__toggle" aria-controls="nav-main" aria-expanded="false">
					<?php
					/**
					 * Filters the svg icon used for the hamburger menu on mobile. Make
					 * sure your replacement icon has alt text or an aria label to make it
					 * accessible.
					 *
					 * @since 1.0.0
					 *
					 * @param string 		The content to show for the button, default an svg
					 * 									hamburger menu icon (three horizontal lines).
					 * @return string		The filtred button content text.
					 */
					echo apply_filters( 'magic_hat_svg_menu_toggle' , '<svg xmlns="http://www.w3.org/2000/svg" aria-label="' . __( 'Toggle main menu', 'magic-hat' ) . '" width="24" height="24" viewBox="0 0 24 24"><path d="M24 6h-24v-4h24v4zm0 4h-24v4h24v-4zm0 8h-24v4h24v-4z"/></svg>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
				<?php wp_nav_menu( array(
					'theme_location' => 'menu-main',
					'container' => 'ul',
					'depth' => 3,
					'menu_id' => 'main-menu',
					'menu_class' => 'nav__list nav__list-desktop-h'
				) ); ?>
			</nav><!-- #nav-main -->
		<?php } ?>
	</header><!-- #masthead -->

	<div id="content" class="site__content">
		<main id="primary" class="content-primary">
			<?php if ( function_exists( 'breadcrumb_trail' ) && get_theme_mod( 'show-breadcrumbs', true ) ) {
				breadcrumb_trail();
			}
