<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Raiden
 */

// Determine site layout and return correct template.
$layout = get_theme_mod( 'site_layout', 'layout-one' );

if ( 'layout-one-ex' == $layout ) {
	$template = 'layout-excerpt';
} else {
	$template = 'layout-simple';
}

get_template_part( 'template-parts/content', esc_attr( $template ) );
