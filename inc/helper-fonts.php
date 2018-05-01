<?php
/**
 * Helper functions for Google fonts.
 *
 * @package Raiden
 */

if ( ! function_exists( 'raiden_get_font_stack' ) ) :
/**
 * Validate the font choice and get a font stack for it.
 *
 * @since  1.0.0.
 *
 * @param  string    $font    The 1st font in the stack.
 * @return string             The full font stack.
 */
function raiden_get_font_stack( $font ) {
	$all_fonts = raiden_get_all_fonts();

	// Sanitize font choice
	$font = raiden_sanitize_font_choice( $font );

	// Standard font
	if ( isset( $all_fonts[ $font ]['stack'] ) && ! empty( $all_fonts[ $font ]['stack'] ) ) {
		$stack = $all_fonts[ $font ]['stack'];
	} elseif ( in_array( $font, raiden_all_font_choices() ) ) {
		$stack = '"' . $font . '","Helvetica Neue",Helvetica,Arial,sans-serif';
	} else {
		$stack = '"Helvetica Neue",Helvetica,Arial,sans-serif';
	}

	/**
	 * Allow developers to filter the full font stack.
	 *
	 * @param string    $stack    The font stack.
	 * @param string    $font     The font.
	 */
	return apply_filters( 'raiden_font_stack', $stack, $font );

	return $stack;
}
endif;

if ( ! function_exists( 'raiden_get_relative_font_size' ) ) :
/**
 * Convert a font size to a relative size based on a starting value and percentage.
 *
 * @since  1.0.0.
 *
 * @param  mixed    $value         The value to base the final value on.
 * @param  mixed    $percentage    The percentage of change.
 * @return float                   The converted value.
 */
function raiden_get_relative_font_size( $value, $percentage ) {
	return round( (float) $value * ( $percentage / 100 ) );
}
endif;

if ( ! function_exists( 'raiden_convert_px_to_rem' ) ) :
/**
 * Given a px value, return a rem value.
 *
 * @since  1.0.0.
 *
 * @param  mixed    $px      The value to convert.
 * @param  mixed    $base    The font-size base for the rem conversion.
 * @return float             The converted value.
 */
function raiden_convert_px_to_rem( $px, $base = 0 ) {
	return (float) $px / 10;
}
endif;

if ( ! function_exists( 'raiden_get_google_font_uri' ) ) :
/**
 * Build the HTTP request URL for Google Fonts.
 *
 * @since  1.0.0.
 *
 * @return string    The URL for including Google Fonts.
 */
function raiden_get_google_font_uri() {
	$keys = array( 'raiden_body_font', 'raiden_header_font' );
	$fonts = array();

	foreach ( $keys as $key ) {
		$fonts[] = get_theme_mod( $key, 'Roboto' );
	}

	// De-dupe the fonts
	$fonts         = array_unique( $fonts );
	$allowed_fonts = raiden_get_google_fonts();
	$family        = array();

	// Validate each font and convert to URL format
	foreach ( $fonts as $font ) {
		$font = trim( $font );

		// Verify that the font exists
		if ( array_key_exists( $font, $allowed_fonts ) ) {
			// Build the family name and variant string (e.g., "Open+Sans:regular,italic,700")
			$family[] = urlencode( $font ) . ':' . join( ',', raiden_choose_google_font_variants( $font, $allowed_fonts[ $font ]['variants'] ) );
		}
	}

	// Convert from array to string
	if ( empty( $family ) ) {
		return '';
	} else {
		$request = '//fonts.googleapis.com/css?family=' . implode( '|', $family );
	}

	// Load the font subset
	$subset = get_theme_mod( 'raider_font_subset', 'latin' );

	if ( 'all' === $subset ) {
		$subsets_available = raiden_get_google_font_subsets();

		// Remove the all set
		unset( $subsets_available['all'] );

		// Build the array
		$subsets = array_keys( $subsets_available );
	} else {
		$subsets = array(
			'latin',
			$subset,
		);
	}

	// Append the subset string
	if ( ! empty( $subsets ) ) {
		$request .= urlencode( '&subset=' . join( ',', $subsets ) );
	}

	/**
	 * Filter the Google Fonts URL.
	 *
	 * @param string    $url    The URL to retrieve the Google Fonts.
	 */
	return apply_filters( 'raiden_get_google_font_uri', esc_url( $request ) );
}
endif;

if ( ! function_exists( 'raiden_choose_google_font_variants' ) ) :
/**
 * Given a font, chose the variants to load for the theme.
 *
 * Attempts to load regular, italic, and 700. If regular is not found, the first variant in the family is chosen. italic
 * and 700 are only loaded if found. No fallbacks are loaded for those fonts.
 *
 * @since  1.0.0.
 *
 * @param  string    $font        The font to load variants for.
 * @param  array     $variants    The variants for the font.
 * @return array                  The chosen variants.
 */
function raiden_choose_google_font_variants( $font, $variants = array() ) {
	$chosen_variants = array();
	if ( empty( $variants ) ) {
		$fonts = raiden_get_google_fonts();

		if ( array_key_exists( $font, $fonts ) ) {
			$variants = $fonts[ $font ]['variants'];
		}
	}

	// If a "regular" variant is not found, get the first variant
	if ( ! in_array( 'regular', $variants ) ) {
		$chosen_variants[] = $variants[0];
	} else {
		$chosen_variants[] = 'regular';
	}

	// Only add "italic" if it exists
	if ( in_array( 'italic', $variants ) ) {
		$chosen_variants[] = 'italic';
	}

	// Only add "300" if it exists
	if ( in_array( '300', $variants ) ) {
		$chosen_variants[] = '300';
	}

	// Only add "700" if it exists
	if ( in_array( '700', $variants ) ) {
		$chosen_variants[] = '700';
	}

	// Only add "900" if it exists
	if ( in_array( '900', $variants ) ) {
		$chosen_variants[] = '900';
	}

	/**
	 * Allow developers to alter the font variant choice.
	 *
	 * @param array     $variants    The list of variants for a font.
	 * @param string    $font        The font to load variants for.
	 * @param array     $variants    The variants for the font.
	 */
	return apply_filters( 'raiden_font_variants', array_unique( $chosen_variants ), $font, $variants );
}
endif;

if ( ! function_exists( 'raiden_sanitize_font_subset' ) ) :
/**
 * Sanitize the Character Subset choice.
 *
 * @since  1.0.0
 *
 * @param  string    $value    The value to sanitize.
 * @return array               The sanitized value.
 */
function raiden_sanitize_font_subset( $value ) {
	if ( ! array_key_exists( $value, raiden_get_google_font_subsets() ) ) {
		$value = 'latin';
	}

	/**
	 * Filter the sanitized subset choice.
	 *
	 * @param string    $value    The chosen subset value.
	 */
	return apply_filters( 'raiden_sanitize_font_subset', $value );
}
endif;

if ( ! function_exists( 'raiden_get_google_font_subsets' ) ) :
	/**
	 * Iterate through all the Google font data and build a list of unique subset options.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function raiden_get_google_font_subsets() {
		$subsets = array();
		$font_data = raiden_get_google_fonts();

		foreach ( $font_data as $font => $data ) {
			if ( isset( $data['subsets'] ) ) {
				$subsets = array_merge( $subsets, (array) $data['subsets'] );
			}
		}

		$subsets = array_unique( $subsets );
		sort( $subsets );

		return $subsets;
	}
endif;

if ( ! function_exists( 'raiden_sanitize_font_choice' ) ) :
/**
 * Sanitize a font choice.
 *
 * @since  1.0.0.
 *
 * @param  string    $value    The font choice.
 * @return string              The sanitized font choice.
 */
function raiden_sanitize_font_choice( $value ) {
	if ( ! is_string( $value ) ) {
		// The array key is not a string, so the chosen option is not a real choice
		return '';
	} else if ( array_key_exists( $value, raiden_all_font_choices() ) ) {
		return $value;
	} else {
		return '';
	}

	/**
	 * Filter the sanitized font choice.
	 *
	 * @param string    $value    The chosen font value.
	 */
	return apply_filters( 'raiden_sanitize_font_choice', $return );
}
endif;

if ( ! function_exists( 'raiden_all_font_choices' ) ) :
/**
 * Packages the font choices into value/label pairs for use with the customizer.
 *
 * @since  1.0.0.
 *
 * @return array    The fonts in value/label pairs.
 */
function raiden_all_font_choices() {
	$fonts   = raiden_get_all_fonts();
	$choices = array();

	// Repackage the fonts into value/label pairs
	foreach ( $fonts as $key => $font ) {
		$choices[ $key ] = $font['label'];
	}

	return $choices;
}
endif;

if ( ! function_exists( 'raiden_get_all_fonts' ) ) :
/**
 * Compile font options from different sources.
 *
 * @since  1.0.0.
 *
 * @return array    All available fonts.
 */
function raiden_get_all_fonts() {
	$heading1            = array( 1 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Standard Fonts', 'raiden' ) ) ) );
	$standard_fonts      = raiden_get_standard_fonts();

	$google_fonts        = raiden_get_google_fonts();

	$serif_heading       = array( 2 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Serif Fonts (Google)', 'raiden' ) ) ) );
	$serif_fonts         = wp_list_filter( $google_fonts, array( 'category' => 'serif' ) );

	$sans_serif_heading  = array( 3 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Sans Serif Fonts (Google)', 'raiden' ) ) ) );
	$sans_serif_fonts    = wp_list_filter( $google_fonts, array( 'category' => 'sans-serif' ) );

	$display_heading     = array( 4 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Display Fonts (Google)', 'raiden' ) ) ) );
	$display_fonts       = wp_list_filter( $google_fonts, array( 'category' => 'display' ) );

	$handwriting_heading = array( 4 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Handwriting Fonts (Google)', 'raiden' ) ) ) );
	$handwriting_fonts   = wp_list_filter( $google_fonts, array( 'category' => 'handwriting' ) );

	$monospace_heading   = array( 4 => array( 'label' => sprintf( '&mdash; %s &mdash;', esc_html__( 'Monospace Fonts (Google)', 'raiden' ) ) ) );
	$monospace_fonts     = wp_list_filter( $google_fonts, array( 'category' => 'monospace' ) );

	return apply_filters( 'raiden_all_fonts', array_merge(
		$heading1, $standard_fonts,
		$serif_heading, $serif_fonts,
		$sans_serif_heading, $sans_serif_fonts,
		$display_heading, $display_fonts,
		$handwriting_heading, $handwriting_fonts,
		$monospace_heading, $monospace_fonts
	) );
}
endif;

if ( ! function_exists( 'raiden_get_standard_fonts' ) ) :
/**
 * Return an array of standard websafe fonts.
 *
 * @since  1.0.0.
 *
 * @return array    Standard websafe fonts.
 */
function raiden_get_standard_fonts() {
	return array(
		'serif' => array(
			'label' => _x( 'Serif', 'font style', 'raiden' ),
			'stack' => 'Georgia,Times,"Times New Roman",serif',
		),
		'sans-serif' => array(
			'label' => _x( 'Sans Serif', 'font style', 'raiden' ),
			'stack' => '"Helvetica Neue",Helvetica,Arial,sans-serif',
		),
		'monospace' => array(
			'label' => _x( 'Monospaced', 'font style', 'raiden' ),
			'stack' => 'Monaco,"Lucida Sans Typewriter	","Lucida Typewriter","Courier New",Courier,monospace',
		)
	);
}
endif;
