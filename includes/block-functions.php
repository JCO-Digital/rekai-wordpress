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
	$data = handle_testing_mode();
	$data = handle_path_options( $data, $attributes );

	// Add site language to only display current language.
	if ( ! empty( $attributes['currentLanguage'] ) ) {
		$data['allowedlangs'] = get_locale();
	}

	$blocked_attributes = array( 'className', 'currentLanguage' );
	foreach ( $attributes as $key => $value ) {
		if ( in_array( $key, $blocked_attributes, true ) ) {
			continue;
		}
		if ( is_bool( $value ) ) {
			$data[ $key ] = $value ? 'true' : 'false';
		} elseif ( ! empty( $value ) && is_array( $value ) ) {
			$data[ $key ] = implode( ',', $value );
		} elseif ( ! empty( $value ) ) {
			$data[ $key ] = $value;
		}
	}

	return map_data_to_dataset( $data );
}

/**
 * Handles path-related options and modifies the provided data array based on the attributes.
 *
 * @param array $data The data array to be modified with path options.
 * @param array $attributes An array of attributes containing path options and limits. Passed by reference to cleanup the attributes.
 *
 * @return array The modified data array reflecting the applied path options.
 */
function handle_path_options( array $data, array &$attributes ): array {
	switch ( $attributes['pathOption'] ) {
		case 'useRoot':
			$data['userootpath'] = 'true';
			break;
		case 'maxDepth':
			$data['maxpathdepth'] = $attributes['depth'] ?? 1;
			break;
		case 'rootPathLevel':
			$data['userootpath']   = 'true';
			$data['rootpathlevel'] = $attributes['depth'] ?? 1;
			break;
		default:
			break;
	}
	switch ( $attributes['limit'] ) {
		case 'subPages':
			$data['excludechildnodes'] = 'true';
			break;
		case 'minDepth':
			$data['minpathdepth'] = $attributes['limitDepth'] ?? 1;
			break;
		default:
			break;
	}
	unset( $attributes['pathOption'], $attributes['limit'], $attributes['depth'], $attributes['limitDepth'] );
	return $data;
}

/**
 * Handles configuration for test mode settings.
 *
 * @param array $data Initial data array to populate with test mode settings.
 * @return array Data array with test mode settings if applicable.
 */
function handle_testing_mode( $data = array() ) {
	// Check for test mode.
	if ( ! RekaiMain::get_instance()->get_test_mode() ) {
		return $data;
	}

	$project_id = get_option( 'rekai_project_id' );
	$secret_key = get_option( 'rekai_secret_key' );

	// Check that both project id and secret key is set.
	if ( empty( $project_id ) || empty( $secret_key ) ) {
		return $data;
	}

	$data['projectid'] = $project_id;
	$data['secretkey'] = $secret_key;

	// Set mock data attribute.
	if ( get_option( 'rekai_use_mock_data' ) === '1' ) {
		$data['advanced_mockdata'] = 'true';
	}

	return $data;
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
function handle_extra_attributes(
	string $attributes_string,
	array &$attributes
): void {
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
