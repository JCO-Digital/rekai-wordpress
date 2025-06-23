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
 * This function transforms block attributes into data attributes that can be used
 * for Rekai's front-end functionality. It processes testing mode settings,
 * handles path options, and includes specific attributes based on block type.
 *
 * @param array $attributes The attributes array to be processed.
 *
 * @return array An array of HTML data attributes ready to be added to elements.
 */
function generate_data_attributes( $attributes ) {
	$data = handle_testing_mode();
	$data = handle_path_options( $attributes, $data );

	$passthrough = array(
		'nrOfHits',
	);

	switch ( $attributes['blockType'] ?? '' ) {
		case 'recommendations':
			$passthrough[] = 'renderStyle';
			switch ( $attributes['renderStyle'] ) {
				case 'list':
					$passthrough[] = 'listCols';
					break;
				case 'advanced':
					$passthrough[] = 'cols';
					$passthrough[] = 'showImage';
					$passthrough[] = 'showIngress';
					$passthrough[] = 'ingressMaxLength';
					break;
			}
			break;
		case 'qna':
			$data['entitytype'] = 'rekai-qna';
			$passthrough[]      = 'tags';
			break;
	}

	// Add site language to only display current language.
	if ( ! empty( $attributes['showLangs'] ) ) {
		if ( empty( $attributes['allowedLangs'] ) ) {
			// Set current language as allowedLangs.
			$data['allowedlangs'] = get_bloginfo( 'language' );
		} else {
			// Pass through set allowedLangs.
			$passthrough[] = 'allowedLangs';
		}
	}
	if ( ! empty( $attributes['showHeader'] ) ) {
		$passthrough[] = 'headerText';
		$passthrough[] = 'headerHeadingLevel';
	}

	foreach ( $passthrough as $key ) {
		if ( ! isset( $attributes[ $key ] ) ) {
			continue;
		}

		$value = $attributes[ $key ];

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
 * Processes path options and limitations from block attributes.
 *
 * This function handles different path configurations and content limitations
 * based on the attributes provided by the block. It configures settings such as
 * root path usage, subtree navigation, and various content limitations.
 *
 * @param array $attributes The block attributes containing configuration options.
 * @param array $data The existing data array to be modified.
 * @return array The updated data array with path and limitation settings.
 */
function handle_path_options( array $attributes, $data = array() ): array {

	// Handle Path Options.
	switch ( $attributes['pathOption'] ?? '' ) {
		case 'rootPath':
			$data['userootpath'] = 'true';
			break;
		case 'subTree':
			if ( ! empty( $attributes['subTree'] ) ) {
				$data['subtree'] = generate_subtree( $attributes['subTree'] );
			}
			break;
		case 'rootPathLevel':
			$data['userootpath']   = 'true';
			$data['rootpathlevel'] = $attributes['rootPathLevel'] ?? 1;
			break;
	}

	// Handle Limitaions.
	switch ( $attributes['limitations'] ?? '' ) {
		case 'subPages':
			if ( ! empty( $attributes['excludeTree'] ) ) {
				$data['excludetree'] = generate_subtree( $attributes['excludeTree'] );
			}
			break;
		case 'childNodes':
			$data['excludechildnodes'] = 'true';
			break;
		case 'onPageLinks':
			$data['filter_exclude_onpagelinks'] = 'true';
			break;
	}
	return $data;
}

/**
 * Handles configuration for test mode settings.
 *
 * Checks if test mode is enabled and adds necessary project ID and secret key
 * to the data array. Also configures mock data if that option is enabled.
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
 * Generates a comma-separated string of links based on an array of IDs.
 *
 * This function takes an array of post IDs, retrieves the permalink for each ID,
 * and constructs a string of permalinks separated by commas. The URL is shortened to a relative path.
 *
 * @param array $ids An array of post IDs.
 * @return string A comma-separated string of permalinks, or an empty string if input is invalid.
 */
function generate_subtree( array $ids ): string {
	$subtree = array();
	foreach ( $ids as $id ) {
		$link      = preg_replace( '|^https?://[^/]+/|', '^/', get_permalink( $id ) );
		$subtree[] = $link ?: $id;
	}
	return implode( ',', $subtree );
}

/**
 * Handles extra attributes provided by the user, merging or adding them to the existing attributes.
 *
 * This function parses a string of HTML-like attributes, extracts attribute names and values,
 * sanitizes the values, and adds them to the existing array of attributes.  It specially handles
 * `data-subtree` attributes by appending values to any existing `data-subtree` attribute. All
 * other attributes are added or overwritten with the user-supplied value.
 *
 * It uses a regular expression to find attributes in the format `attribute="value"`.
 *
 * Note: The `attributes` array is passed by reference and will be modified directly.
 *
 * @param string $attributes_string The user-supplied attributes string.
 * @param array  &$attributes The array of attributes to add the user-supplied attributes. This will be updated by reference.
 *
 * @return void
 */
function handle_extra_attributes(
	string $attributes_string,
	array &$attributes
): void {
	if (
		preg_match_all(
			'/([a-zA-Z0-9_-]+)="([^"]*)"/',
			$attributes_string,
			$attr_array,
			PREG_SET_ORDER
		)
	) {
		foreach ( $attr_array as $match ) {
			$attr_name     = $match[1];
			$cleaned_value = esc_attr( str_replace( array( '"', "'" ), '', $match[2] ) );
			$old_value     = $attributes[ $attr_name ] ?? '';
			switch ( $attr_name ) {
				case 'data-subtree':
					$attributes['data-subtree'] = ( empty( $old_value ) ? '' : $old_value . ',' ) . $cleaned_value;
					break;
				default:
					$attributes[ $attr_name ] = $cleaned_value;
					break;
			}
		}
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
		$dataset[ 'data-' . strtolower( $key ) ] = $value;
	}
	return $dataset;
}

/**
 * Converts an array of data attributes into an HTML attribute string.
 *
 * @param array $data The array of data attributes, where keys are attribute names and values are attribute values.
 * @return string An HTML attribute string containing the data attributes.
 */
function dataset_to_attributes( $data ) {
	$output = '';
	foreach ( $data as $key => $value ) {
		$output .= ' ' . $key . '="' . esc_attr( $value ) . '"';
	}
	return $output;
}
