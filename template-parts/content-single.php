<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Raiden
 */

$thumbnail_url = get_the_post_thumbnail_url();

$post_classes = 'post-tile single-post-cover';
if ( has_post_thumbnail() ) $post_classes .= ' has-post-thumbnail';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="<?php echo esc_attr( $post_classes ); ?>" <?php if ( has_post_thumbnail() ) echo 'style="background-image:url('. esc_url( $thumbnail_url ) .')"'; ?>>
		<header class="entry-header">
			<?php
				if ( is_single() ) {
					the_title( '<h1 class="entry-title">', '</h1>' );
				} else {
					the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
				}

			if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<?php raiden_posted_on(); ?>
			</div><!-- .entry-meta -->
			<?php
			endif; ?>
		</header><!-- .entry-header -->
	</div>


	<div class="entry-content">
		<?php
			the_content( sprintf(
				/* translators: %s: Name of current post. */
				wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'raiden' ), array( 'span' => array( 'class' => array() ) ) ),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'raiden' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->

<?php get_template_part( 'template-parts/secondary', 'post-meta' ); ?>
