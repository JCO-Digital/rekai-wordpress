<?php
/**
 * Rek.ai shortcodes.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generates and returns a prediction shortcode. It passes the shortcode attributes to the HTML
 * element as data attributes.
 *
 * @param array $atts Shortcode attributes.
 * @return string Generated HTML for embed.
 */
function prediction( $atts ) {

	$dataset = generate_data_attributes( $atts );

	// Return custom embed code.
	return '<div class="rek-prediction" ' . dataset_to_attributes( $dataset ) . '></div>';
}
add_shortcode( 'rekai-prediction', '\Rekai\prediction' );


/**
 * Generates and returns a questions and answers shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string Generated HTML for embed.
 */
function qna( $atts ) {
	$atts['entitytype'] = 'rekai-qna';
	$atts['blockType']  = 'qna';

	if ( 'server' === resolve_qna_render_mode() ) {
		$post_id = get_the_ID();
		if ( empty( $post_id ) && isset( $GLOBALS['post']->ID ) ) {
			$post_id = $GLOBALS['post']->ID;
		}
		$ssr_html = fetch_qna_ssr_html( (int) $post_id, $atts );

		// Deliberately not falling back to prediction() (client-side rendering) here: it
		// scopes results differently (broader "related content" matching rather than this
		// exact page), so a fallback would silently show a different result set than what
		// SSR was configured to show - render an empty block instead.
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- see blocks/src/recommendations/render.php.
		return $ssr_html ?? '<div class="rek-prediction--ssr"></div>';
	}

	return prediction( $atts );
}
add_shortcode( 'rekai-qna', '\Rekai\qna' );
