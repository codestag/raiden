<?php
/**
 * Template Name: Custom Archives
 *
 * @package Raiden
 */

get_header(); ?>

	<div id="primary" class="content-area">

		<header class="page-header archive-page-title">
			<h1 class="page-title">
				<span><?php _e( 'Page:', 'raiden' ); ?></span>
				<?php _e( 'Archive', 'raiden' ); ?>
			</h1>
		</header>

		<main id="main" class="site-main" role="main">

			<section class="custom-archive-widgets">
				<div class="archive-widget custom-recent-posts">
					<h2 class="widget-title"><?php esc_attr_e( 'Latest Posts', 'mono' ); ?></h2>

					<?php

					rewind_posts();

					$q = new WP_Query( array(
						'post_type'           => 'post',
						'post_status'         => 'publish',
						'posts_per_page'      => '5',
						'ignore_sticky_posts' => true,
					) );

					if ( $q->have_posts() ):
						echo '<ul class="custom-widget custom-post-list">';

						while ( $q->have_posts() ) : $q->the_post(); ?>
						<li>
							<h2 class="entry-title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="post-meta">
								<?php raiden_posted_on(); ?>
							</div>
						</li>
						<?php endwhile;

						echo '</ul>';
					endif;
					?>
				</div>

				<?php
					the_widget( 'WP_Widget_Categories', 'count=0', array(
						'before_widget' => '<div class="archive-widget custom-categories">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					) );

					the_widget( 'WP_Widget_Tag_Cloud', '', array(
						'before_widget' => '<div class="archive-widget custom-tags">',
						'after_widget'  => '</div>',
						'before_title'  => '<h2 class="widget-title">',
						'after_title'   => '</h2>',
					) );
				?>
			</section>

		</main>

	</div>

<?php get_footer(); ?>
