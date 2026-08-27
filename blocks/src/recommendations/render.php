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

$block_type  = $attributes['blockType'] ?? '';
$render_mode = 'qna' === $block_type ? resolve_qna_render_mode( $attributes['qnaRenderMode'] ?? 'default' ) : 'client';
$ssr_html    = null;

if ( 'server' === $render_mode ) {
	$current_page_id = (int) ( $block->context['postId'] ?? get_the_ID() );
	$ssr_html        = fetch_qna_ssr_html( $current_page_id, $attributes );
}

if ( null !== $ssr_html ) :
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'rek-prediction rek-prediction--ssr' ) );
	?>
	<div <?php echo wp_kses_data( $wrapper_attributes ); ?>>
		<?php
		/*
		 * render_qna_ssr_markup() already esc_html()s question/answer text; this string also
		 * includes a required <script type="application/ld+json"> that wp_kses()/wp_kses_post()
		 * would strip (kses always strips <script>, regardless of allowed-tags config).
		 */
		echo $ssr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
	<?php
elseif ( 'server' === $render_mode ) :
	/*
	 * Server-side rendering was requested but returned nothing (no matching Q&A, or a fetch
	 * failure). Deliberately rendering an empty block rather than falling back to the
	 * client-side script here: that script scopes results differently (broader "related
	 * content" matching rather than this exact page), so a fallback would silently show a
	 * different result set than what SSR was configured to show - more confusing than
	 * an empty block.
	 */
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes( array( 'class' => 'rek-prediction rek-prediction--ssr' ) ) ); ?>></div>
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
