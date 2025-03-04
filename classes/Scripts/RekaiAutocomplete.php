<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Handles the Rek.ai autocomplete scripts.
 *
 * @package Rekai
 */

namespace Rekai\Scripts;

use Rekai\Scripts\RekaiBase;
use function Rekai\render_template;

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
		$autocomplete_enabled = get_option( 'rekai_autocomplete_enabled' ) === '1';
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
	}

	/**
	 * Handles rendering head inline scripts.
	 *
	 * @return void
	 */
	final public function render_head(): void {
		if ( ! $this->should_load() ) {
			return;
		}
		$is_automatic          = get_option( 'rekai_autocomplete_automatic' ) === '1';
		$selector              = get_option( 'rekai_autocomplete_automatic_selector' );
		$autocomplete_navigate = get_option( 'rekai_autocomplete_navigate_on_click' ) === '1';
		$autocomplete_options  = $this->get_autocomplete_options();

		$data = array(
			'is_automatic'          => $is_automatic,
			'autocomplete_selector' => $selector,
			'autocomplete_options'  => $autocomplete_options,
			'autocomplete_navigate' => $autocomplete_navigate,
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template(
			'rekai-autocomplete-head',
			$data
		);
	}

	/**
	 * Handles retrieving the autocomplete options and constructing the JSON string.
	 *
	 * @return string
	 */
	private function get_autocomplete_options(): string {
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
			$options['params']['allowedlangs'] = get_locale();
		}

		return wp_json_encode( $options );
	}
}
