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

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
	<figure class="post-thumbnail">
		<?php the_post_thumbnail( 'full' ); ?>
	</figure>
	<?php endif; ?>

	<div class="content-wrap">
		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php raiden_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<header class="entry-header">
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
				/* translators: %s: Name of current post */
				the_content( sprintf(
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'raiden' ),
					get_the_title()
				) );

				wp_link_pages( array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'raiden' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'raiden' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				) );
			?>
		</div><!-- .entry-content -->
	</div>

</article><!-- #post-## -->
