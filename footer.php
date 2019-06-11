<?php
/**
 * Site Footer
 *
 * The template file that shows the site footer.
 *
 * @todo Limit number of widgets allowed in the footer area?
 * @todo Move the inline js to external files or encode as CDATA
 *
 * @package Magic Hat
 * @since 1.0.0
 */
?>
	</div><!-- #content -->

	<footer class="site__footer">
		<div class="footer__info">
			<div class="info__widget">
				<?php dynamic_sidebar( 'info' ); ?>
			</div><!-- .info__widget -->
			<div class="info__legal">
				<?php if ( function_exists( 'magic_hat_customize_partial_copyright' ) ) magic_hat_customize_partial_copyright(); ?>

				<?php if ( get_theme_mod( 'show-credits', true ) || is_customize_preview() ) {
					magic_hat_theme_credit();
				} ?>

				<?php if ( has_nav_menu( 'menu-footer' ) ) { ?>
					<nav aria-label="<?php esc_html_e( 'Footer navigation', 'magic-hat' ); ?>" id="nav-footer" class="nav-footer">
						<?php wp_nav_menu( array(
							'theme_location' => 'menu-footer',
							'container' => 'ul',
							'depth' => 1,
							'menu_class' => 'nav__list nav__list-h',
						) ); ?>
					</nav><!-- #nav-footer -->
				<?php } ?>
			</div><!-- .info__legal -->
		</div><!-- .footer__info -->
		<div class="footer__widget">
			<?php dynamic_sidebar( 'footer' ); ?>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
