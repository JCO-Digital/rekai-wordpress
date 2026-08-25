<?php
/**
 * File handler.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ssr_html = null;

if ( 'qna' === ( $attributes['blockType'] ?? '' ) && 'server' === resolve_qna_render_mode( $attributes['qnaRenderMode'] ?? 'default' ) ) {
	$current_page_id = (int) ( $block->context['postId'] ?? get_the_ID() );
	$ssr_html        = fetch_qna_ssr_html( $current_page_id, $attributes );
}

if ( null !== $ssr_html ) :
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'rek-prediction rek-prediction--ssr' ) );
	?>
	<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
		<?php
		/*
		 * Trusted response from our own authenticated Rek.ai API call; includes a required
		 * <script type="application/ld+json"> that wp_kses()/wp_kses_post() would strip
		 * (kses always strips <script>, regardless of allowed-tags config).
		 */
		echo $ssr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
	<?php
else :
	$extra          = generate_data_attributes( $attributes ?? array() );
	$extra['class'] = 'rek-prediction';
	handle_extra_attributes( $attributes['extraAttributes'] ?? '', $extra );
	?>
	<div
			<?php
			echo wp_kses_data( get_block_wrapper_attributes( $extra ) );
			?>
	></div>
	<?php
endif;
