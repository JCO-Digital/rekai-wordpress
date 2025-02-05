<?php
/**
 * Block functions for handling data attributes and string conversions.
 *
 * @package Rekai
 */

namespace Rekai;

use Rekai\Scripts\RekaiMain;

/**
 * Generates data attributes for the Rekai configuration.
 *
 * @param array $attributes The attributes array to be processed.
 * @return array
 */
function generate_data_attributes( $attributes ) {
	$is_test   = RekaiMain::get_instance()->get_test_mode();
	$mock_data = get_option( 'rekai_use_mock_data' );
	$add_test  = false;

	if ( $is_test ) {
			$project_id = get_option( 'rekai_project_id' );
			$secret_key = get_option( 'rekai_secret_key' );
			$add_test   = ! empty( $project_id ) && ! empty( $secret_key );
	}

	$data = array();
	if ( $is_test && $add_test ) {
		$data['projectid'] = $project_id;
		$data['secretkey'] = $secret_key;
	}
	if ( $is_test && $add_test && $mock_data === '1' ) {
		$data['advanced_mockdata'] = 'true';
	}

	$block = array( 'className' );

	foreach ( $attributes as $key => $value ) {
		if ( ! in_array( $key, $block ) ) {
			$data[ $key ] = $value;
		}
	}

	return map_data_to_dataset( $data );
}


/**
 * Maps an array of data to HTML5 data attributes.
 *
 * @param array $data The data array to be mapped.
 * @return array The mapped dataset array with 'data-' prefixes.
 */
function map_data_to_dataset( $data ) {
	$dataset = array();
	foreach ( $data as $key => $value ) {
		$dataset[ 'data-' . $key ] = $value;
	}
	return $dataset;
}
