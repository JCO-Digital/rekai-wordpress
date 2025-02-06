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
 *
 * @return array
 */
function generate_data_attributes( $attributes ) {
	$data = array();

	// Check for test mode.
	if ( RekaiMain::get_instance()->get_test_mode() ) {
		$project_id = get_option( 'rekai_project_id' );
		$secret_key = get_option( 'rekai_secret_key' );

		// Check that both project id and secret key is set.
		if ( ! empty( $project_id ) && ! empty( $secret_key ) ) {
			$data['projectid'] = $project_id;
			$data['secretkey'] = $secret_key;

			// Set mock data attribute.
			if ( get_option( 'rekai_use_mock_data' ) === '1' ) {
				$data['advanced_mockdata'] = 'true';
			}
		}
	}

	// Add site language to only display current language.
	if ( ! empty( $attributes['currentLanguage'] ) ) {
		$data['allowedlangs'] = get_locale();
	}

	$block = array( 'className', 'currentLanguage' );

	foreach ( $attributes as $key => $value ) {
		if ( ! in_array( $key, $block, true ) ) {
			if ( is_bool( $value ) ) {
				$data[ $key ] = $value ? 'true' : 'false';
			} elseif ( ! empty( $value ) && is_array( $value ) ) {
				$data[ $key ] = implode( ',', $value );
			} elseif ( ! empty( $value ) ) {
				$data[ $key ] = $value;
			}
		}
	}

	return map_data_to_dataset( $data );
}

/**
 * Handles parsing a user supplied string of attributes. It will validate them and sanitize them correctly (?).
 *
 * @param string $attributes_string The user-supplied attributes string.
 * @param array  $attributes The array of attributes to add the user-supplied attributes.
 *                           This will be updated by reference.
 *
 * @return void
 */
function handle_extra_attributes( string $attributes_string, array &$attributes ): void {
	$attr_array = array();
	preg_match_all(
		'/([a-zA-Z0-9_-]+)="([^"]*)"/',
		$attributes_string,
		$attr_array,
		PREG_SET_ORDER
	);
	if ( empty( $attributes['class'] ) ) {
		$attributes['class'] = '';
	}
	foreach ( $attr_array as $match ) {
		if ( count( $match ) !== 3 ) {
			continue;
		}
		$cleaned_value = str_replace( array( '"', "'" ), '', $match[2] );
		if ( $match[1] === 'class' ) {
			$attributes['class'] .= ' ' . esc_attr( $cleaned_value );
			continue;
		}
		$attributes[ $match[1] ] = esc_attr( $cleaned_value );
	}
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
