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
	 * Add the Theme Options section.
	 */
	$wp_customize->add_panel( 'raiden_options_panel', array(
		'title'       => esc_html__( 'Theme Options', 'raiden' ),
		'description' => esc_html__( 'Configure your theme settings', 'raiden' ),
	) );

	// Color Schemes Settings.
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

	// Site Layout Settings Panel.
	$wp_customize->add_section( 'raiden_site_layout_settings', array(
		'title' => esc_html__( 'Site Layout', 'raiden' ),
		'panel' => 'raiden_options_panel',
	) );

	// Site Layout Settings.
	$wp_customize->add_setting( 'site_layout', array(
		'default'           => 'layout-one',
		'transport'         => 'refresh',
		'sanitize_callback' => 'raiden_sanitize_layout',
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

	// Google fonts.
	$wp_customize->add_section( 'raiden_fonts', array(
		'title' => esc_html__( 'Fonts', 'raiden' ),
		'panel' => 'raiden_options_panel',
		'description' => sprintf(
					esc_html__( 'The list of Google fonts is long! You can %s before making your choices.', 'raiden' ),
					sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( 'https://fonts.google.com' ),
						esc_html__( 'preview', 'raiden' )
					)
				)
	) );

	$google_fonts = raiden_all_font_choices();

	// Body Font selector.
	$wp_customize->add_setting( 'raiden_body_font', array(
		'default'           => 'Roboto',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'raiden_body_font', array(
		'label'    => esc_html__( 'Body Font', 'raiden' ),
		'section'  => 'raiden_fonts',
		'type'     => 'select',
		'choices'  => $google_fonts,
	) );

	// Header Font selector.
	$wp_customize->add_setting( 'raiden_header_font', array(
		'default'           => 'Roboto',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'raiden_header_font', array(
		'label'    => esc_html__( 'Header Font', 'raiden' ),
		'section'  => 'raiden_fonts',
		'type'     => 'select',
		'choices'  => $google_fonts,
	) );

	// Header Font selector.
	$wp_customize->add_setting( 'raider_font_subset', array(
		'default'           => '13',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'raider_font_subset', array(
		'label'    => esc_html__( 'Character Subset', 'raiden' ),
		'section'  => 'raiden_fonts',
		'type'     => 'select',
		'choices'  => raiden_get_google_font_subsets(),
		'description' => sprintf(
					esc_html__( 'Not all fonts provide each of these subsets. Please visit the %s to see which subsets are available for each font.', 'raiden' ),
					sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( 'https://fonts.google.com' ),
						esc_html__( 'Google Fonts website', 'raiden' )
					)
				),
	) );
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
		'light' => array(
			'label'  => esc_html__( 'Light', 'raiden' ),
			'colors' => array(
				'#ffffff',
				'#ffffff',
				'#000000',
				'#ffffff',
				'#000000',
				'#000000',
				'#000000',
			),
		),
		'dark' => array(
			'label'  => esc_html__( 'Dark', 'raiden' ),
			'colors' => array(
				'#16161c',
				'#16161c',
				'#ffffff',
				'#1b1c25',
				'#ffffff',
				'#238ec0',
				'#238ec0',
			),
		),
		'green' => array(
			'label'  => esc_html__( 'Green', 'raiden' ),
			'colors' => array(
				'#2a5020',
				'#4d7a41',
				'#ffffff',
				'#ffffff',
				'#000000',
				'#4d7a41',
				'#2a5020',
			),
		),
		'yellow' => array(
			'label'  => esc_html__( 'Yellow', 'raiden' ),
			'colors' => array(
				'#ededed',
				'#ffd155',
				'#1d190d',
				'#ffffff',
				'#000000',
				'#ffd155',
				'#1d190d',
			),
		),
		'blue' => array(
			'label'  => esc_html__( 'Blue', 'raiden' ),
			'colors' => array(
				'#195fa0',
				'#2277c7',
				'#ffffff',
				'#ffffff',
				'#000000',
				'#2277c7',
				'#195fa0',
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
		'background_color'         => $color_scheme[0],
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
 * Returns CSS for the Google Fonts.
 *
 * @since Raiden 1.0
 *
 * @return string Google Fonts CSS.
 */
function raiden_google_fonts_css() {
	$body_font   = get_theme_mod( 'raiden_body_font', 'Roboto' );
	$header_font = get_theme_mod( 'raiden_header_font', 'Roboto' );

	$fonts_css = <<<CSS
	/* Body Font */
	body {
		font-family: {$body_font},"Helvetica Neue",Helvetica,Arial,sans-serif;
	}
	/* Header Font */
	h1, h2, h3, h4, h5, h6 {
		font-family: {$header_font},Georgia,Times,"Times New Roman",serif;
	}
CSS;

	wp_add_inline_style( 'raiden-style', $fonts_css );

}
add_action( 'wp_enqueue_scripts', 'raiden_google_fonts_css' );

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
	.author-info {
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
	.stag-button,
	.stag-button[disabled]:hover,
	.stag-button[disabled]:focus,
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
					<label for="<?php echo $this->id;  ?>[key]">

						<?php if ( '1-1-1-1' === $value ) : ?>
						<svg width="80px" height="62px" viewBox="342 413 80 62" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						    <path d="M342,413 L422,413 L422,427 L342,427 L342,413 Z M342,429 L422,429 L422,443 L342,443 L342,429 Z M342,445 L422,445 L422,459 L342,459 L342,445 Z M342,461 L422,461 L422,475 L342,475 L342,461 Z" id="Combined-Shape" stroke="none" fill="#D8D8D8" fill-rule="evenodd"></path>
						</svg>

						<?php elseif ( '1-2-1-2' === $value ) : ?>
						<svg width="81px" height="62px" viewBox="0 0 81 62" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						    <g id="Group-2" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						        <g id="Group" fill="#D8D8D8">
						            <path d="M0,0 L80,0 L80,14 L0,14 L0,0 Z M0,32 L80,32 L80,46 L0,46 L0,32 Z M0,16 L39,16 L39,30 L0,30 L0,16 Z M41,16 L80,16 L80,30 L41,30 L41,16 Z M0,48 L39,48 L39,62 L0,62 L0,48 Z M41,48 L80,48 L80,62 L41,62 L41,48 Z" id="Combined-Shape"></path>
						        </g>
						    </g>
						</svg>

						<?php elseif ( '1-excerpt' === $value ) : ?>
						<svg width="80px" height="62px" viewBox="546 413 80 62" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
						    <g id="Group-3" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" transform="translate(546.000000, 413.000000)">
						        <path d="M0,0 L80,0 L80,14 L0,14 L0,0 Z M20,16 L60,16 L60,19 L20,19 L20,16 Z M20,48 L60,48 L60,51 L20,51 L20,48 Z M20,22 L60,22 L60,25 L20,25 L20,22 Z M20,54 L60,54 L60,57 L20,57 L20,54 Z M20,27 L60,27 L60,30 L20,30 L20,27 Z M20,59 L60,59 L60,62 L20,62 L20,59 Z M0,32 L80,32 L80,46 L0,46 L0,32 Z" id="Combined-Shape" fill="#D8D8D8"></path>
						    </g>
						</svg>
						<?php endif; ?>

					</label>
				</li>
				<?php
			}

			?> </ul> <?php
		}
	}
}
endif; // raiden_customizer_layout_control

add_action( 'customize_register', 'raiden_customizer_layout_control', 1, 1 );

if ( ! function_exists( 'blink_customizer_layout_css' ) ) :
/**
 * Add CSS for customizer layout picker.
 *
 * @return void
 */
function blink_customizer_layout_css() {
	?>

	<style type="text/css">
		.customizer-control-row {
			position: relative;
			display: inline-block;
			vertical-align: top;
		}

		.customizer-control-row input[type="radio"] {
			position: absolute;
			width: 100%;
			height: 100%;
			opacity: 0;
		}
		.customizer-control-row input,
		.customizer-control-row label {
			width: 100%;
		}
		.customizer-control-row label {
			height: 75px;
			display: block;
			box-sizing: border-box;
		}

		.customizer-control-row input[type="radio"]:checked + label path {
			fill: #a9a9a9;
		}
	</style>

	<?php
}
endif;

add_action( 'customize_controls_print_scripts', 'blink_customizer_layout_css' );

if ( ! function_exists( 'raiden_sanitize_layout' ) ) :
/**
 * Sanitize customizer options for layout selector.
 *
 * @return void
 */
function raiden_sanitize_layout( $value ) {
	$layouts = array( 'layout-one', 'layout-odd', 'layout-one-ex' );
	if ( ! array_key_exists( $value, array_flip( $layouts ) ) ) {
		$value = 'layout-one';
	}

	return $value;
}
endif; // raiden_sanitize_layout.

/**
 * Enqueues front-end CSS for the sidebar background color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_sidebar_background_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[1];
	$sidebar_background_color = get_theme_mod( 'sidebar_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $sidebar_background_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Sidebar Background Color */
	.sidebar { background-color: %1$s; }
	.author-info { background-color: %1$s; }
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $sidebar_background_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_sidebar_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the sidebar text color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_sidebar_text_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[2];
	$sidebar_text_color = get_theme_mod( 'sidebar_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $sidebar_text_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Sidebar Text Color */
	.sidebar { color: %1$s; }
	.author-info { color: %1$s; }
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $sidebar_text_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_sidebar_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the content background color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_content_background_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[3];
	$content_background_color = get_theme_mod( 'content_background_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $content_background_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Content Background Color */
	.site-content { background-color: %1$s; }
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $content_background_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_content_background_color_css', 11 );

/**
 * Enqueues front-end CSS for the content text color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_content_text_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[4];
	$content_text_color = get_theme_mod( 'content_text_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $content_text_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Content Text Color */
	.site-content { color: %1$s; }
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $content_text_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_content_text_color_css', 11 );

/**
 * Enqueues front-end CSS for the link color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_link_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[5];
	$link_color = get_theme_mod( 'link_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $link_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Link Color */
	a { color: %1$s; }
	.layout-one-ex .entry-meta { background-color: %1$s !important; }
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $link_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_link_color_css', 11 );


/**
 * Enqueues front-end CSS for the button color.
 *
 * @since Raiden 1.0.2
 *
 * @see wp_add_inline_style()
 */
function raiden_button_color_css() {
	$color_scheme    = raiden_get_color_scheme();
	$default_color   = $color_scheme[6];
	$button_color = get_theme_mod( 'button_color', $default_color );

	// Don't do anything if the current color is the default.
	if ( $button_color === $default_color ) {
		return;
	}

	$css = '
	/* Custom Button Color */
	button,
	button[disabled]:hover,
	button[disabled]:focus,
	.stag-button,
	.stag-button[disabled]:hover,
	.stag-button[disabled]:focus,
	input[type="button"],
	input[type="button"][disabled]:hover,
	input[type="button"][disabled]:focus,
	input[type="reset"],
	input[type="reset"][disabled]:hover,
	input[type="reset"][disabled]:focus,
	input[type="submit"],
	input[type="submit"][disabled]:hover,
	input[type="submit"][disabled]:focus {
		background-color: %1$s;
	}
	';

	wp_add_inline_style( 'raiden-style', sprintf( $css, $button_color ) );
}

add_action( 'wp_enqueue_scripts', 'raiden_button_color_css', 11 );
