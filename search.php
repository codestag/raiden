<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Raiden
 */

get_header(); ?>

	<section id="primary" class="content-area">

		<?php if ( have_posts() ) : ?>
		<header class="page-header">
			<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'raiden' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		</header><!-- .page-header -->
		<?php endif; ?>

		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) :
			?>

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_format() );

			endwhile;

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->

		<?php
		if ( have_posts() ) :
			the_posts_navigation();
		endif;
		?>
	</section><!-- #primary -->

<?php
get_footer();
