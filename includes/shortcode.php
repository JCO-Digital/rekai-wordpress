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
	return prediction( $atts );
}
add_shortcode( 'rekai-qna', '\Rekai\qna' );
