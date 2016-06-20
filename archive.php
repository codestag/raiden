<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Raiden
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<?php if ( have_posts() ) : ?>
		<header class="page-header archive-page-title">
			<h1 class="page-title">
				<?php echo raiden_the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			</h1>

			<?php if ( is_author() ) : ?>
				<div class="author-description">
					<?php the_author_meta( 'description' ); ?>
				</div>
			<?php endif; ?>

			<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
		</header><!-- .page-header -->
		<?php endif; ?>

		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->

		<?php if ( have_posts() ) :
			the_posts_navigation();
		endif; ?>
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
