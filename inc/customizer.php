<?php
/**
 * Raiden Theme Customizer.
 *
 * @package Raiden
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function raiden_customize_register( $wp_customize ) {
	$color_scheme = raiden_get_color_scheme();

	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	/**
	 * Add the Theme Options section
	 */
	$wp_customize->add_panel( 'raiden_options_panel', array(
		'title'       => esc_html__( 'Theme Options', 'raiden' ),
		'description' => esc_html__( 'Configure your theme settings', 'raiden' ),
	) );

	// Color Schemes Settings
	$wp_customize->add_section( 'raiden_color_schemes', array(
		'title' => esc_html__( 'Color Schemes', 'raiden' ),
		'panel' => 'raiden_options_panel',
	) );

	// Add color scheme setting and control.
	$wp_customize->add_setting( 'color_scheme', array(
		'default'           => 'default',
		'sanitize_callback' => 'raiden_sanitize_color_scheme',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'color_scheme', array(
		'label'    => esc_html__( 'Base Color Scheme', 'raiden' ),
		'section'  => 'raiden_color_schemes',
		'type'     => 'select',
		'choices'  => raiden_get_color_scheme_choices(),
		'priority' => 1,
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background_color', array(
		'label'       => esc_html__( 'Background Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add sidebar background color setting and control.
	$wp_customize->add_setting( 'sidebar_background_color', array(
		'default'           => $color_scheme[1],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_background_color', array(
		'label'       => esc_html__( 'Sidebar Background Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add sidebar text color setting and control.
	$wp_customize->add_setting( 'sidebar_text_color', array(
		'default'           => $color_scheme[2],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'sidebar_text_color', array(
		'label'       => esc_html__( 'Sidebar Text Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add content background color setting and control.
	$wp_customize->add_setting( 'content_background_color', array(
		'default'           => $color_scheme[3],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_background_color', array(
		'label'       => esc_html__( 'Content Background Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add content text color setting and control.
	$wp_customize->add_setting( 'content_text_color', array(
		'default'           => $color_scheme[4],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'content_text_color', array(
		'label'       => esc_html__( 'Content Text Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add site link color setting and control.
	$wp_customize->add_setting( 'link_color', array(
		'default'           => $color_scheme[5],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'link_color', array(
		'label'       => esc_html__( 'Link Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Add button color setting and control.
	$wp_customize->add_setting( 'button_color', array(
		'default'           => $color_scheme[6],
		'sanitize_callback' => 'sanitize_hex_color',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'button_color', array(
		'label'       => esc_html__( 'Button Color', 'raiden' ),
		'section'     => 'raiden_color_schemes',
	) ) );

	// Site Layout Settings Panel
	$wp_customize->add_section( 'raiden_site_layout_settings', array(
		'title' => esc_html__( 'Site Layout', 'raiden' ),
		'panel' => 'raiden_options_panel',
	) );

	// Site Layout Settings
	$wp_customize->add_setting( 'site_layout', array(
		'default'   => 'layout-one',
		'transport' => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Layout_Control( $wp_customize, 'site_layout', array(
		'label'       => esc_html__( 'Site Layout', 'raiden' ),
		'choices'   => array(
			'layout-one'    => '1-1-1-1',
			'layout-odd'    => '1-2-1-2',
			'layout-one-ex' => '1-excerpt',
		),
		'section'     => 'raiden_site_layout_settings',
	) ) );
}
add_action( 'customize_register', 'raiden_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function raiden_customize_preview_js() {
	wp_enqueue_script( 'raiden_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'raiden_customize_preview_js' );

/**
 * Binds the JS listener to make Customizer color_scheme control.
 *
 * Passes color scheme data as colorScheme global.
 *
 * @since Raiden 1.0
 */
function raiden_customize_control_js() {
	wp_enqueue_script( 'color-scheme-control', get_template_directory_uri() . '/js/color-scheme-control.js', array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), '20160604', true );
	wp_localize_script( 'color-scheme-control', 'colorScheme', raiden_get_color_schemes() );
}
add_action( 'customize_controls_enqueue_scripts', 'raiden_customize_control_js' );

/**
 * Registers color schemes for Raiden.
 *
 * Can be filtered with {@see 'raiden_color_schemes'}.
 *
 * The order of colors in a colors array:
 * 1. Sidebar Background Color.
 * 2. Link Color.
 * 3. Button Color.
 * 4. Sidebar Text Color.
 * 5. Main Text Color.
 *
 * @since Raiden 1.0
 *
 * @return array An associative array of color scheme options.
 */
function raiden_get_color_schemes() {
	return apply_filters( 'raiden_color_scheme', array(
		'default' => array(
			'label'  => esc_html__( 'Default', 'raiden' ),
			'colors' => array(
				'#24252f',
				'#00b796',
				'#ffffff',
				'#ffffff',
				'#252323',
				'#00b796',
				'#33c5ab',
			),
		),
		'dark' => array(
			'label'  => esc_html__( 'Dark', 'raiden' ),
			'colors' => array(
				'#262626',
				'#1a1a1a',
				'#9adffd',
				'#e5e5e5',
				'#c1c1c1',
				'#c1c1c1',
				'#c1c1c1',
			),
		),
	) );
}

if ( ! function_exists( 'raiden_get_color_scheme' ) ) :
/**
 * Retrieves the current Raiden color scheme.
 *
 * Create your own raiden_get_color_scheme() function to override in a child theme.
 *
 * @since Raiden 1.0
 *
 * @return array An associative array of either the current or default color scheme HEX values.
 */
function raiden_get_color_scheme() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );
	$color_schemes       = raiden_get_color_schemes();

	if ( array_key_exists( $color_scheme_option, $color_schemes ) ) {
		return $color_schemes[ $color_scheme_option ]['colors'];
	}

	return $color_schemes['default']['colors'];
}
endif; // raiden_get_color_scheme

if ( ! function_exists( 'raiden_get_color_scheme_choices' ) ) :
/**
 * Retrieves an array of color scheme choices registered for Raiden.
 *
 * Create your own raiden_get_color_scheme_choices() function to override
 * in a child theme.
 *
 * @since Raiden 1.0
 *
 * @return array Array of color schemes.
 */
function raiden_get_color_scheme_choices() {
	$color_schemes                = raiden_get_color_schemes();
	$color_scheme_control_options = array();

	foreach ( $color_schemes as $color_scheme => $value ) {
		$color_scheme_control_options[ $color_scheme ] = $value['label'];
	}

	return $color_scheme_control_options;
}
endif; // raiden_get_color_scheme_choices

if ( ! function_exists( 'raiden_sanitize_color_scheme' ) ) :
/**
 * Handles sanitization for Raiden color schemes.
 *
 * Create your own raiden_sanitize_color_scheme() function to override
 * in a child theme.
 *
 * @since Raiden 1.0
 *
 * @param string $value Color scheme name value.
 * @return string Color scheme name.
 */
function raiden_sanitize_color_scheme( $value ) {
	$color_schemes = raiden_get_color_scheme_choices();

	if ( ! array_key_exists( $value, $color_schemes ) ) {
		return 'default';
	}

	return $value;
}
endif; // raiden_sanitize_color_scheme

/**
 * Enqueues front-end CSS for color scheme.
 *
 * @since Raiden 1.0
 *
 * @see wp_add_inline_style()
 */
function raiden_color_scheme_css() {
	$color_scheme_option = get_theme_mod( 'color_scheme', 'default' );

	// Don't do anything if the default color scheme is selected.
	if ( 'default' === $color_scheme_option ) {
		return;
	}

	$color_scheme = raiden_get_color_scheme();

	// Convert main text hex color to rgba.
	$color_textcolor_rgb = raiden_hex2rgb( $color_scheme[4] );

	// If the rgba values are empty return early.
	if ( empty( $color_textcolor_rgb ) ) {
		return;
	}

	// If we get this far, we have a custom color scheme.
	$colors = array(
		'background_color'    => $color_scheme[0],
		'sidebar_background_color' => $color_scheme[1],
		'sidebar_text_color'       => $color_scheme[2],
		'content_background_color' => $color_scheme[3],
		'content_text_color'       => $color_scheme[4],
		'link_color'               => $color_scheme[5],
		'button_color'             => $color_scheme[6],
	);

	$color_scheme_css = raiden_get_color_scheme_css( $colors );

	wp_add_inline_style( 'raiden-style', $color_scheme_css );
}
add_action( 'wp_enqueue_scripts', 'raiden_color_scheme_css' );

/**
 * Returns CSS for the color schemes.
 *
 * @since Raiden 1.0
 *
 * @param array $colors Color scheme colors.
 * @return string Color scheme CSS.
 */
function raiden_get_color_scheme_css( $colors ) {
	$colors = wp_parse_args( $colors, array(
		'background_color'    => '',
		'sidebar_background_color' => '',
		'sidebar_text_color'       => '',
		'content_background_color' => '',
		'content_text_color'       => '',
		'link_color'               => '',
		'button_color'             => '',
	) );

	return <<<CSS
	/* Color Scheme */
	body {
		background-color: {$colors['background_color']};
	}

	/* Sidebar Text & Background Color */
	.sidebar {
		background-color: {$colors['sidebar_background_color']};
		color: {$colors['sidebar_text_color']};
	}

	/* Content Text & Background Color */
	.site-content {
		background-color: {$colors['content_background_color']};
		color: {$colors['content_text_color']};
	}

	/* Link Color */
	a {
		color: {$colors['link_color']};
	}
	.layout-one-ex .entry-meta {
		background-color: {$colors['link_color']} !important;
	}

	/* Button Color */
	button,
	button[disabled]:hover,
	button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus {
		background-color: {$colors['button_color']};
	}

CSS;
}

/**
 * Outputs an Underscore template for generating CSS for the color scheme.
 *
 * The template generates the css dynamically for instant display in the
 * Customizer preview.
 *
 * @since Raiden 1.0
 */
function raiden_color_scheme_css_template() {
	$colors = array(
		'background_color'         => '{{ data.background_color }}',
		'sidebar_background_color' => '{{ data.sidebar_background_color }}',
		'sidebar_text_color'       => '{{ data.sidebar_text_color }}',
		'content_background_color' => '{{ data.content_background_color }}',
		'content_text_color'       => '{{ data.content_text_color }}',
		'link_color'               => '{{ data.link_color }}',
		'button_color'             => '{{ data.button_color }}',
	);
	?>
	<script type="text/html" id="tmpl-raiden-color-scheme">
		<?php echo raiden_get_color_scheme_css( $colors ); ?>
	</script>
	<?php
}
add_action( 'customize_controls_print_footer_scripts', 'raiden_color_scheme_css_template' );

if ( ! function_exists( 'raiden_customizer_layout_control' ) ) :
/**
 * Layout Picker Control
 *
 * Attach the custom layout picker control to the `customize_register` action
 * so the WP_Customize_Control class is initiated.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function raiden_customizer_layout_control( $wp_customize ) {
	class WP_Customize_Layout_Control extends WP_Customize_Control {
		public $type = 'layout';

		public function render_content() {
			?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<ul>
			<?php

			foreach ( $this->choices as $key => $value ) {
				?>
				<li class="customizer-control-row">
					<input type="radio" value="<?php echo esc_attr( $key ) ?>" name="<?php echo $this->id; ?>" <?php echo $this->link(); ?> <?php if ( $this->value() === $key ) echo 'checked="checked"'; ?>>
					<label for="<?php echo $this->id;  ?>[key]"><?php echo $value; ?></label>
				</li>
				<?php
			}

			?> </ul> <?php
		}
	}
}
endif; // raiden_customizer_layout_control

add_action( 'customize_register', 'raiden_customizer_layout_control', 1, 1 );
