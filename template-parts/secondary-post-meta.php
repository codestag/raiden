<?php
/**
 * Post Meta.
 *
 * @package Raiden
 */

$tags_list = get_the_tag_list( '', esc_html__( ', ', 'raiden' ) );
$sharing = function_exists( 'sharing_display' );

// Bail, if no modules are visible.
if ( ! $tags_list && ! $sharing ) return;

?>

<div class="entry-footer">
	<div class="grid">
		<?php if ( $tags_list ) : ?>
			<div class="post-tags">
				<?php
					printf(
						esc_html__( 'Tags: %1$s', 'mono' ),
						$tags_list
					);
				?>
			</div>
		<?php endif; ?>

		<?php if ( $sharing ) : ?>
			<div class="post-share <?php if ( ! $tags_list ) echo 'full-span'; ?>">
				<?php sharing_display( '', true ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
