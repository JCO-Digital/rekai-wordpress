<?php
/**
 * Handles the Rek.ai autocomplete scripts.
 *
 * @package Rekai
 */

namespace Rekai\Scripts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rekai\Scripts\RekaiBase;

/**
 * Handles the Rek.ai scripts.
 *
 * @since 0.1.0
 */
class RekaiAutocomplete extends RekaiBase {

	/**
	 * Returns a boolean whether the scripts/assets should be loaded.
	 * Checks against the Plugin settings.
	 *
	 * @return bool
	 */
	final public function should_load(): bool {
		$autocomplete_enabled = get_option( 'rekai_autocomplete_mode', 'disabled' ) !== 'disabled';
		if ( ! $autocomplete_enabled ) {
			return false;
		}

		return parent::should_load();
	}

	/**
	 * Handles the Rek.ai scripts.
	 *
	 * @return void
	 */
	final public function enqueue(): void {
		if ( ! $this->should_load() ) {
			return;
		}

		wp_enqueue_script(
			'rekai-autocomplete',
			$this->get_static_url( '/addon/v3/rekai_autocomplete.min.js' ),
			array( 'rekai-main' ),
			'1',
			false
		);
		$this->create_inline( 'rekai-autocomplete' );
	}

	/**
	 * Handles rendering head inline scripts.
	 *
	 * @param string $handle The script handle to add the inline script to.
	 *
	 * @return void
	 */
	final public function create_inline( $handle ): void {
		if ( ! $this->should_load() ) {
			return;
		}
		$is_automatic          = get_option( 'rekai_autocomplete_mode' ) === 'auto';
		$selector              = get_option( 'rekai_autocomplete_automatic_selector' );
		$autocomplete_navigate = get_option( 'rekai_autocomplete_navigate_on_click' ) === '1';
		$autocomplete_options  = $this->get_autocomplete_options();

		$output = '';

		if ( $is_automatic && $selector ) {
			$output .= sprintf(
				'
					const rekAutocomplete = rekai_autocomplete(
						"%s",
						%s
					);',
				esc_js( $selector ),
				wp_json_encode( $autocomplete_options )
			);
			if ( $autocomplete_navigate ) {
				$output .= '
					rekAutocomplete.on("rekai_autocomplete:selected", function (event, suggestion) {
						window.location.href = suggestion.url;
					});
				';
			}
			wp_add_inline_script(
				$handle,
				'__rekai.ready(function () {' . $output . '});',
				'after'
			);
		}
	}

	/**
	 * Handles retrieving the autocomplete options and constructing and array.
	 *
	 * @return array
	 */
	private function get_autocomplete_options(): array {
		$options           = array();
		$is_test           = $this->get_test_mode();
		$options['params'] = array();

		if ( $is_test ) {
			$options['params']['advanced_mockdata'] = get_option( 'rekai_use_mock_data' ) === '1';
			$options['projectid']                   = get_option( 'rekai_project_id' ) ?? '';
			$options['srek']                        = get_option( 'rekai_secret_key' ) ?? '';
		}

		$options['params']['nrofhits'] = (int) get_option( 'rekai_autocomplete_nrofhits', 10 );

		if ( '1' === get_option( 'rekai_autocomplete_usecurrentlang' ) ) {
			$options['params']['allowedlangs'] = get_bloginfo( 'language' );
		}

		return $options;
	}
}
