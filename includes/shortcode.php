<?php
/**
 * Rek.ai shortcodes.
 *
 * @package Rekai
 */

namespace Rekai;

/**
 * Generates and returns a prediction shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string Generated HTML for embed.
 */
function prediction( $atts ) {

	$dataset = generate_data_attributes( $atts );

	// Return custom embed code.
	return '<div class="rek-prediction" ' . dataset_to_attributes( $dataset ) . '></div>';
}
add_shortcode( 'rek-prediction', '\Rekai\prediction' );


/**
 * Generates and returns a questions and answers shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return string Generated HTML for embed.
 */
function qna( $atts ) {
	$atts['entitytype']        = 'rekai-qna';
	$atts['advanced_mockdata'] = true;
	return prediction( $atts );
}
add_shortcode( 'rek-qna', '\Rekai\qna' );
