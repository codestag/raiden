<?php
/**
 * WordPress.com-specific functions and definitions
 * This file is centrally included from `wp-content/mu-plugins/wpcom-theme-compat.php`.
 *
 * @package Raiden
 */

/**
 * Adds support for WP.com print styles
 */
function raiden_theme_support() {

	global $themecolors;

	/**
	 * Set a default theme color array for WP.com.
	 *
	 * @global array $themecolors
	 */
	if ( ! isset( $themecolors ) ) :
		$themecolors = array(
			'bg'     => '24252f',
			'border' => '252323',
			'text'   => '000000',
			'link'   => '33c5ab',
			'url'    => '33c5ab',
		);
	endif;

	add_theme_support( 'print-style' );
}
add_action( 'after_setup_theme', 'raiden_theme_support' );
