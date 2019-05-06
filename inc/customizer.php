<?php
/**
 * Magic Hat Theme Customizer
 *
 * Some of the default sections are renamed, and Colors has been removed.
 *
 * @link https://codex.wordpress.org/Theme_Customization_API
 *
 * @since 1.0.0
 * @package Magic Hat
 * @subpackage Customizer
 */

/**
 * Customize Custom Controls
 */
if ( class_exists( 'WP_Customize_Control' ) ) :
	require get_template_directory() . '/inc/customize/class-magic-hat-customize-heading-control.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	require get_template_directory() . '/inc/customize/class-magic-hat-customize-image-radio-control.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
	require get_template_directory() . '/inc/customize/class-magic-hat-customize-toggle-control.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
endif;

/**
 * Loads the CSS for custom control types.
 *
 * @since 1.0.0
 */
function magic_hat_customize_control_css() {
	wp_enqueue_style( 'magic-hat-customize-controls', get_template_directory_uri() . '/assets/css/customize-controls.css', array(), null );
}
add_action( 'customize_controls_init', 'magic_hat_customize_control_css' );

/**
 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
 *
 * @since 1.0.0
 */
function magic_hat_customize_preview_js() {
	wp_enqueue_script( 'magic-hat-customize-preview', get_template_directory_uri() . '/assets/js/customize-preview.min.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'magic_hat_customize_preview_js' );

/**
 * Hides certain controls depending on the current page context.
 *
 * @since 1.0.0
 */
function magic_hat_customize_context_js() {
	wp_enqueue_script( 'magic-hat-customize-context', get_template_directory_uri() . '/assets/js/customize-context.min.js', array(), null, true );
}
add_action( 'customize_controls_enqueue_scripts', 'magic_hat_customize_context_js', 100 );

/**
 * Registers support for certain Customizer options and features
 *
 * @since 1.0.0
 */
function magic_hat_support_customizer() {
	/**
	 * Custom logo upload
	 * @link https://developer.wordpress.org/themes/functionality/custom-logo/
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
		'header-text' => array( 'branding-title' ),
	) );

	/**
	 * Custom header upload
	 * @link https://developer.wordpress.org/themes/functionality/custom-headers
	 * @todo custom header color
	 */
	add_theme_support( 'custom-header', apply_filters( 'magic_hat_custom_header_args', array(
		'default-image' => '',
		'default-text-color' => '000000',
		'width' => 1000,
		'height' => 250,
		'flex-height' => true,
		'flex-width' => true,
	) ) );

	add_theme_support( 'custom-background', apply_filters( 'magic_hat_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
		'default-preset' => 'fit',
		'wp-head-callback' => '__return_empty_string',
	) ) );

	add_theme_support( 'customize-selective-refresh-widgets' );
}
add_action( 'after_setup_theme', 'magic_hat_support_customizer' );

/**
 * Prints out a style attribute for the page body populated by custom background
 * properties set in the Customizer.
 *
 * @since 1.0.0
 */
function magic_hat_custom_bg() {
	if ( ! get_theme_mod( 'use-boxed', false ) ) {
		return;
	}

	$style = '';
	$color = get_theme_mod( 'background_color', false );
	$image = get_theme_mod( 'background_image', false );

	if ( ! $color && ! $image ) {
		return;
	}
	if ( $color ) {
		$style .= 'background-color: #' . $color . ';';
	}
	if ( $image ) {
		$style .= '
		background-image: url(\'' . esc_url( $image ) . '\');
		background-attachment: ' . get_theme_mod( 'background_attachment', 'fixed' ) . ';
		background-position: ' . get_theme_mod( 'background_position_x', 'top' ) . ' ' . get_theme_mod( 'background_position_y', '' ) . ';
		background-repeat: ' . get_theme_mod( 'background_repeat', 'no-repeat' ) . ';
		background-size: ' . get_theme_mod( 'background_size', 'cover' ) . ';';
	}
	echo 'style="' . esc_attr( $style ) . '"';
}

/**
 * Registers and adjusts the Customizer settings, controls, and sections for the theme.
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function magic_hat_customize_register( $wp_customize ) {
	/* Sections */
	$wp_customize->add_section( 'magic_hat_options' , array(
		'title'      => __( 'Theme Options', 'magic-hat' ),
		'priority'   => 0,
	) );

	$wp_customize->get_section( 'header_image' )->title = esc_html__( 'Header', 'magic-hat' );
	$wp_customize->get_section( 'background_image' )->title = esc_html__( 'Body', 'magic-hat' );
	$wp_customize->get_section( 'static_front_page' )->title = esc_html__( 'Posts & Pages', 'magic-hat' );
	$wp_customize->get_section( 'static_front_page' )->description = esc_html__( 'Choose what to show on your homepage.', 'magic-hat' );
	$wp_customize->remove_section( 'colors' );

	$wp_customize->add_section( 'magic_hat_footer' , array(
		'title'      => __( 'Footer', 'magic-hat' ),
		'priority'   => 130,
	) );

	/* Settings & Controls */

	$wp_customize->add_setting( 'use-underline', array(
		'default' => 0,
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'use-underline', array(
		'label' => esc_html__( 'Use accessible link styles', 'magic-hat' ),
		'section' => 'magic_hat_options',
		'description' => esc_html__( 'Add underlines to links to make them easier to spot for sighted users.', 'magic-hat' ),
	) ) );

	$wp_customize->add_setting( 'show-breadcrumbs', array(
		'default' => 1,
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'show-breadcrumbs', array(
		'label' => esc_html__( 'Show breadcrumbs', 'magic-hat' ),
		'section' => 'magic_hat_options',
		'description' => esc_html__( 'Show a breadcrumb trail above the page content.', 'magic-hat' ),
	) ) );

	$wp_customize->add_setting( 'use-printstyles', array(
		'default' => 1,
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'use-printstyles', array(
		'label' => esc_html__( 'Load print styles', 'magic-hat' ),
		'section' => 'magic_hat_options',
		'description' => esc_html__( 'Load styles to make your web pages print-friendly.', 'magic-hat' ),
	) ) );

	$wp_customize->add_setting( 'use-lightbox', array(
		'default' => 1,
		'transport' => 'postMessage',
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'use-lightbox', array(
		'label' => esc_html__( 'Enable Lightbox', 'magic-hat' ),
		'description' => esc_html__( 'Turn this off if you want to use your own lightbox plugin or hate modal windows.', 'magic-hat' ),
		'section' => 'magic_hat_options',
	) ) );

	$wp_customize->add_setting( 'show-credits', array(
		'default' => 1,
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'show-credits', array(
		'label' => esc_html__( 'Show Theme Credits', 'magic-hat' ),
		'section' => 'magic_hat_options',
	) ) );

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	$wp_customize->add_setting( 'copyright' , array(
		/* translators: (c) {year} {site name}, all rights reserved */
		'default' => sprintf( __( '&copy; %1$d %2$s all rights reserved', 'magic-hat' ), date('Y'), get_option( 'blogname' ) ),
		'sanitize_callback' => 'magic_hat_sanitize_html',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( 'copyright', array(
		'label' => esc_html__( 'Copyright Text', 'magic-hat' ),
		'section' => 'title_tagline',
		'type' => 'text',
		'priority' => 99,
	) );

	$wp_customize->add_setting( 'use-boxed', array(
		'default' => 0,
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'use-boxed', array(
		'label' => esc_html__( 'Use Boxed Layout', 'magic-hat' ),
		'section' => 'background_image',
		'priority' => 0,
	) ) );

	$wp_customize->remove_control( 'background_preset' );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
		'label'   => __( 'Background Color', 'magic-hat' ),
		'section' => 'background_image',
		'priority' => 2,
	) ) );

	$wp_customize->add_setting( 'sidebar-side', array(
		'default' => 'sidebar-right',
		'sanitize_callback' => 'magic_hat_sanitize_image_radio',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Image_Radio_Control( $wp_customize, 'sidebar-side', array(
		'label' => __( 'Sidebar Side', 'magic-hat' ),
		'section' => 'background_image',
		'choices' => array(
			'sidebar-left' => array(
				'image' => trailingslashit( get_template_directory_uri() ) . 'assets/img/sidebar-left.png',
				'name' => __( 'Left Sidebar', 'magic-hat' ),
			),
			'' => array(
				'image' => trailingslashit( get_template_directory_uri() ) . 'assets/img/sidebar-none.png',
				'name' => __( 'No Sidebar', 'magic-hat' ),
			),
			'sidebar-right' => array(
				'image' => trailingslashit( get_template_directory_uri() ) . 'assets/img/sidebar-right.png',
				'name' => __( 'Right Sidebar', 'magic-hat' ),
			)
		)
	) ) );

	$wp_customize->add_setting( '404-title' , array(
		'default' => '404',
		'sanitize_callback' => 'magic_hat_sanitize_html',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( '404-title', array(
		'label' => esc_html__( 'Not found title', 'magic-hat' ),
		'section' => 'static_front_page',
		'description' => esc_html__( 'Customize the title text of the 404 page.', 'magic-hat' ),
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'show-nextprev-posts', array(
		'default' => 1,
		'transport' => 'postMessage',
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'show-nextprev-posts', array(
		'label' => esc_html__( 'Show Next/Previous Posts', 'magic-hat' ),
		'section' => 'static_front_page',
	) ) );

	/* Dummy setting used to relay the active_callback state to postMessage settings */
	$wp_customize->add_setting( 'has-comments', array() );

	$wp_customize->add_control( 'has-comments', array(
		'active_callback' => 'magic_hat_show_comment_settings',
		'section' => 'static_front_page',
		'type' => 'text',
	) );

	$wp_customize->add_setting( 'use-comments-ajax', array(
		'default' => 1,
		'transport' => 'postMessage',
		'sanitize_callback' => 'magic_hat_sanitize_toggle',
	) );

	$wp_customize->add_control( new Magic_Hat_Customize_Toggle_Control( $wp_customize, 'use-comments-ajax', array(
		'label' => esc_html__( 'Comment Ajax', 'magic-hat' ),
		'description' => esc_html__( 'Load an extra script to allow comment posting and deletion without refreshing the page.', 'magic-hat' ),
		'section' => 'static_front_page',
	) ) );

	$wp_customize->add_setting( 'comments-title' , array(
		'default' => esc_html__( 'Comments', 'magic-hat' ),
		'sanitize_callback' => 'magic_hat_sanitize_html',
		'transport' => 'postMessage',
	) );

	$wp_customize->add_control( 'comments-title', array(
		'label' => esc_html__( 'Comment Section Title', 'magic-hat' ),
		'section' => 'static_front_page',
		'type' => 'text',
		'description' => esc_html__( 'Customize the title that appears above the comments.', 'magic-hat' ),
	) );

	$wp_customize->add_setting( 'reply-title' , array(
		'default' => esc_html__( 'Leave a reply', 'magic-hat' ),
		'sanitize_callback' => 'magic_hat_sanitize_html',
		'transport' => 'postMessage'
	) );

	$wp_customize->add_control( 'reply-title', array(
		'label' => esc_html__( 'Reply Title', 'magic-hat' ),
		'section' => 'static_front_page',
		'type' => 'text',
		'description' => esc_html__( 'Customize the title that appears above the comment form.', 'magic-hat' ),
	) );

	/* Selective Refresh Setup */
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site__title a',
			'render_callback' => 'magic_hat_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site__desc',
			'render_callback' => 'magic_hat_customize_partial_blogdescription',
		) );
		$wp_customize->selective_refresh->add_partial( '404-title', array(
			'selector'        => '.page__title-error',
			'render_callback' => 'magic_hat_customize_partial_404',
		) );
		$wp_customize->selective_refresh->add_partial( 'comments-title', array(
			'selector'        => '.comments__title',
			'render_callback' => 'magic_hat_customize_partial_comments',
		) );
		$wp_customize->selective_refresh->add_partial( 'reply-title', array(
			'selector'        => '.comment-reply-title',
			'render_callback' => 'magic_hat_customize_partial_reply',
		) );
		$wp_customize->selective_refresh->add_partial( 'copyright', array(
			'selector'        => '.copyright',
			'render_callback' => 'magic_hat_customize_partial_copyright',
		) );
	}
}
add_action( 'customize_register', 'magic_hat_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function magic_hat_customize_partial_blogname() {
	bloginfo( 'name' );
}

function magic_hat_customize_partial_404() {
	echo esc_html( get_theme_mod( '404-title' ) );
}

function magic_hat_show_comment_settings() {
	if ( is_singular() && comments_open() ) {
		return true;
	} else {
		return false;
	}
}

function magic_hat_customize_partial_comments() {
	echo esc_html( get_theme_mod( 'comments-title' ) );
}

function magic_hat_customize_partial_reply() {
	echo esc_html( get_theme_mod( 'reply-title' ) );
}

function magic_hat_customize_partial_copyright() {
	?>
	<span class="legal-copyright"><?php echo wp_kses_post( get_theme_mod( 'copyright' ) ); ?></span>
	<?php
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function magic_hat_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Radio Button and Select sanitization
 *
 * @author Anthony Hortin <http://maddisondesigns.com>
 * @license GPL-2.0+
 * @version 1.0.5
 * @link https://github.com/maddisondesigns
 *
 * @param string $input																			The value to check.
 * @param Magic_Hat_Customize_Image_Radio_Control $setting	Setting object.
 * @return string																						Sanitized value.
 */
function magic_hat_sanitize_image_radio( $input, $setting ) {
	$choices = $setting->manager->get_control( $setting->id )->choices;
	if ( array_key_exists( $input, $choices ) ) {
		return $input;
	} else {
		return $setting->default;
	}
}

/**
 * Sanitizes the value of a toggle control.
 *
 * @param bool $input	The toggle value to sanitize and save as a theme setting.
 * @return int			1 for on, 0 for off.
 */
function magic_hat_sanitize_toggle( $input ) {
	if ( true === $input ) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * Runs text through a fairly generous wp_kses array of allowed HTML tags.
 *
 * @param string $input	The text/HTML to sanitize and save as a theme setting.
 */
function magic_hat_sanitize_html( $input ) {
	return wp_kses( $input, array(
		'a' => array(
			'class' => array(),
			'href' => array(),
			'id' => array(),
			'style' => array(),
			'title' => array()
		),
		'address' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'b' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'bdi' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'bdo' => array(
			'class',
			'dir',
			'id',
			'style',
			'title'
		),
		'br' => array(),
		'code' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'del' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'em' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'i' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'ins' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'kbd' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'mark' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'progress' => array(
			'class',
			'id',
			'max',
			'style',
			'title',
			'value'
		),
		'rp' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'rt' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'ruby' => array(
			'class',
			'id',
			'style',
			'title'
		),
		's' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'small' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'span' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'strong' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'sub' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'sup' => array(
			'class',
			'id',
			'style',
			'title'
		),
		'time' => array(
			'class',
			'datetime',
			'id',
			'style',
			'title'
		),
		'u' => array(
			'class',
			'id',
			'style',
			'title'
		)
	) );
}
