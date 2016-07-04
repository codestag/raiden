<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Raiden
 */

$thumbnail_url = get_the_post_thumbnail_url();

$post_classes = '';
if ( has_post_thumbnail() ) {
	$post_classes .= 'post-tile single-post-cover has-post-thumbnail';
} else {
	$post_classes .=  'no-page-cover';
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="<?php echo esc_attr( $post_classes ); ?>" <?php if ( has_post_thumbnail() ) echo 'style="background-image:url(' . esc_url( $thumbnail_url ) . ')"'; ?>>
		<div class="link-overlay"></div>

		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	</div>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'raiden' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
