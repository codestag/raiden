<?php
/**
 * Jetpack Compatibility File.
 *
 * @link https://jetpack.com/
 *
 * @package Raiden
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 */
function raiden_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'render'    => 'raiden_infinite_scroll_render',
		'footer'    => 'page',
		'wrapper'   => 'false',
	) );

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );
}
add_action( 'after_setup_theme', 'raiden_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function raiden_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();
		if ( is_search() ) :
		    get_template_part( 'template-parts/content', 'search' );
		else :
		    get_template_part( 'template-parts/content', get_post_format() );
		endif;
	}
}

if ( ! function_exists( 'raiden_remove_jetpack_share' ) ) :
/**
 * Remove default output of Jetpack Sharing buttons.
 *
 * @return void
 */
function raiden_remove_jetpack_share() {
	remove_filter( 'the_content', 'sharing_display', 19 );
	remove_filter( 'the_excerpt', 'sharing_display', 19 );
}
endif;

add_action( 'loop_start', 'raiden_remove_jetpack_share' );
