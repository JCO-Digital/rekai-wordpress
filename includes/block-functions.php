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
 * @param array $data Optional array of data attributes. Default empty array.
 * @return array
 */
function generate_data_attributes( $data = array() ) {
	$is_test   = RekaiMain::get_instance()->get_test_mode();
	$mock_data = get_option( 'rekai_use_mock_data' );
	$add_test  = false;

	if ( $is_test ) {
			$project_id = get_option( 'rekai_project_id' );
			$secret_key = get_option( 'rekai_secret_key' );
			$add_test   = ! empty( $project_id ) && ! empty( $secret_key );
	}

	if ( $is_test && $add_test ) {
		$data['projectid'] = $project_id;
		$data['secretkey'] = $secret_key;
	}
	if ( $is_test && $add_test && $mock_data === '1' ) {
		$data['advanced_mockdata'] = 'true';
	}
	return $data;
}
