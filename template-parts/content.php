<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Raiden
 */

$thumbnail_url = get_the_post_thumbnail_url();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-tile' ); ?> <?php if ( has_post_thumbnail() ) echo 'style="background-image:url('. esc_url( $thumbnail_url ) .')"'; ?>>

	<a href="<?php the_permalink(); ?>">
	<div class="link-overlay"></div>
	</a>

	<header class="entry-header">
		<?php
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

		if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php raiden_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php
		endif; ?>
	</header><!-- .entry-header -->

</article><!-- #post-## -->
