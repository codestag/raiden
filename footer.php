<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Raiden
 */

?>

		<footer id="colophon" class="site-footer" role="contentinfo">
			<div class="site-info">
				<a href="<?php echo esc_url( 'https://wordpress.org/' ); ?>">
									<?php
									printf(
										/* translators: %s: WordPress */
										esc_html__( 'Proudly powered by %s', 'raiden' ),
										'WordPress'
									);
									?>
						</a>
				<span class="sep"> | </span>
				<?php
				printf(
					'%1$s by <a href="%2$s" rel="designer">%3$s</a>.',
					'Theme: Raiden',
					esc_url( 'https://codestag.com' ),
					'Codestag'
				);
				?>
			</div><!-- .site-info -->
		</footer><!-- #colophon -->

	</div><!-- #content -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
