<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Raiden
 */

if ( ! function_exists( 'raiden_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function raiden_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';

	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'raiden' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>&nbsp;'; // WPCS: XSS OK.

	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'raiden' ) );
		if ( $categories_list && raiden_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'In %1$s', 'raiden' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}
	}

}
endif;

if ( ! function_exists( 'raiden_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function raiden_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'raiden' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tags: %1$s', 'raiden' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function raiden_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'raiden_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'raiden_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so raiden_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so raiden_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in raiden_categorized_blog.
 */
function raiden_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'raiden_categories' );
}
add_action( 'edit_category', 'raiden_category_transient_flusher' );
add_action( 'save_post',     'raiden_category_transient_flusher' );

if ( ! function_exists( 'raiden_post_navigation' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function raiden_post_navigation() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<div class="container">
			<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'raiden' ); ?></h1>
			<div class="nav-links grid">
				<?php
					previous_post_link(
						'<div class="nav-previous nav-link">%link</div>',
						_x( '<span class="meta-nav">Previous Post</span> <h2 class="post-title">%title</h2>', 'Previous post link', 'raiden' )
					);

					next_post_link(
						'<div class="nav-next nav-link">%link</div>',
						_x( '<span class="meta-nav">Next Post</span> <h2 class="post-title">%title</h2>', 'Next post link', 'raiden' )
					);
				?>
			</div><!-- .nav-links -->
		</div>
	</nav><!-- .navigation -->
	<?php
}
endif; // raiden_post_navigation

function raiden_the_archive_title() {
	if ( is_category() ) {
		$title = sprintf( __( '<span>Category:</span> %s', 'raiden' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( '<span>Tag:</span> %s', 'raiden' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( '<span>Author:</span> %s', 'raiden' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( '<span>Year:</span> %s', 'raiden' ), get_the_date( _x( 'Y', 'yearly archives date format', 'raiden' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( '<span>Month:</span> %s', 'raiden' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'raiden' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( '<span>Day:</span> %s', 'raiden' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'raiden' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'raiden' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'raiden' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( '<span>Archives:</span> %s', 'raiden' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( __( '<span>%1$s:</span> %2$s', 'raiden' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = __( 'Archives', 'raiden' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @since 4.1.0
	 *
	 * @param string $title Archive title to be displayed.
	 */
	return apply_filters( 'get_the_archive_title', $title );
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Adds backwards compatibility for wp_body_open() introduced with WordPress 5.2
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_body_open/
	 * @return void
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}