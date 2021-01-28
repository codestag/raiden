<?php
/**
 * Raiden functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Raiden
 */

if ( ! function_exists( 'raiden_fs' ) ) {
	// Create a helper function for easy SDK access.
	function raiden_fs() {
		global $raiden_fs;

		if ( ! isset( $raiden_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/freemius/start.php';

			$raiden_fs = fs_dynamic_init(
				array(
					'id'               => '7655',
					'slug'             => 'raiden',
					'type'             => 'theme',
					'public_key'       => 'pk_095bcb0dea0807a93fa6f3e4b6e4f',
					'is_premium'       => false,
					'has_addons'       => false,
					'has_paid_plans'   => false,
					'is_org_compliant' => false,
					'menu'             => array(
						'first-path' => 'themes.php',
						'account'    => false,
						'support'    => false,
					),
				)
			);
		}

		return $raiden_fs;
	}

	// Init Freemius.
	raiden_fs();
	// Signal that SDK was initiated.
	do_action( 'raiden_fs_loaded' );
}

/**
 * The current version of the theme.
 */
define( 'RAIDEN_VERSION', '1.2.3' );

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 670; /* pixels */
}

if ( ! function_exists( 'raiden_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function raiden_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Raiden, use a find and replace
		 * to change 'raiden' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'raiden', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for custom logo.
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 270,
				'width'       => 270,
				'flex-height' => true,
			)
		);

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary', 'raiden' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 * See https://developer.wordpress.org/themes/functionality/post-formats/
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
			)
		);

		// Set up the WordPress core custom background feature.
		$color_scheme             = raiden_get_color_scheme();
		$default_background_color = trim( $color_scheme[0], '#' );
		add_theme_support(
			'custom-background',
			apply_filters(
				'raiden_custom_background_args',
				array(
					'default-color' => $default_background_color,
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add Gutenberg Support.
		 *
		 * @since 1.1.0.
		 */
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );

		add_editor_style( '/assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'raiden_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function raiden_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'raiden_content_width', 670 );
}
add_action( 'after_setup_theme', 'raiden_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function raiden_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Widget Area', 'raiden' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'raiden' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'raiden_widgets_init' );

/**
 * Enqueue Gutenberg Styles & Scripts.
 */
function raiden_block_editor_styles() {

	// Google fonts.
	if ( '' !== $google_request = raiden_get_google_font_uri() ) {
		// Enqueue the fonts.
		wp_enqueue_style(
			'raiden-google-fonts',
			$google_request,
			array(),
			RAIDEN_VERSION
		);
	}

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/assets/css/genericons/genericons.css', array(), '3.4.1' );

	$font_header = get_theme_mod( 'raiden_header_font', 'Roboto' );
	$font_body   = get_theme_mod( 'raiden_body_font', 'Roboto' );

	$link_color       = get_theme_mod( 'link_color', '#00b796' );
	$text_color       = get_theme_mod( 'content_text_color', '#252323' );
	$content_bg_color = get_theme_mod( 'content_background_color', '#ffffff' );

	wp_add_inline_style(
		'raiden-google-fonts',
		"
		.editor-styles-wrapper {
			--link-color: {$link_color};
			--text-color: {$text_color};
			--content-bg-color: {$content_bg_color};
			--font-body: '{$font_body}';
			--font-header: '{$font_header}';
		}"
	);
}
add_action( 'enqueue_block_editor_assets', 'raiden_block_editor_styles' );



/**
 * Enqueue scripts and styles.
 */
function raiden_scripts() {
	$style_dependencies = array();

	// Google fonts
	if ( '' !== $google_request = raiden_get_google_font_uri() ) {
		// Enqueue the fonts
		wp_enqueue_style(
			'raiden-google-fonts',
			$google_request,
			$style_dependencies,
			RAIDEN_VERSION
		);
		$style_dependencies[] = 'raiden-google-fonts';
	}

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/assets/css/genericons/genericons.css', array(), '3.4.1' );
	$style_dependencies[] = 'genericons';

	wp_enqueue_style( 'raiden-style', get_stylesheet_uri(), $style_dependencies, RAIDEN_VERSION );

	wp_enqueue_script( 'raiden-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array( 'jquery' ), '20151215', true );

	wp_enqueue_script( 'raiden-skip-link-focus-fix', get_template_directory_uri() . '/assets/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'raiden_scripts' );

/**
 * Converts a HEX value to RGB.
 *
 * @since Raiden 1.0
 *
 * Borrowed from Twenty Sixteen.
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function raiden_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ) . substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ) . substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ) . substr( $color, 2, 1 ) );
	} elseif ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array(
		'red'   => $r,
		'green' => $g,
		'blue'  => $b,
	);
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

require get_template_directory() . '/inc/google-fonts.php';
require get_template_directory() . '/inc/helper-fonts.php';
