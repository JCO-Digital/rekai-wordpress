<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Handles the Rek.ai autocomplete scripts.
 *
 * @package Rekai
 */

namespace Rekai\Scripts;

use Rekai\Singleton;
use function Rekai\render_template;

/**
 * Handles the Rek.ai scripts.
 *
 * @since 0.1.0
 */
class RekaiAutocomplete extends Singleton {

	/**
	 * Initializes the script loading.
	 */
	protected function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_head', array( $this, 'render_head' ), 99 );
	}

	/**
	 * Returns a boolean whether the scripts/assets should be loaded.
	 * Checks against the Plugin settings.
	 *
	 * @return bool
	 */
	final public function should_load(): bool {
		$is_enabled           = get_option( 'rekai_is_enabled' ) === '1';
		$autocomplete_enabled = get_option( 'rekai_autocomplete_enabled' ) === '1';

		return ! ( ! $is_enabled || ! $autocomplete_enabled );
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
			'https://static.rekai.se/addon/v3/rekai_autocomplete.min.js',
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
		$is_automatic         = get_option( 'rekai_autocomplete_automatic' ) === '1';
		$selector             = get_option( 'rekai_autocomplete_automatic_selector' );
		$autocomplete_options = $this->get_autocomplete_options();

		$data = array(
			'is_automatic'          => $is_automatic,
			'autocomplete_selector' => $selector,
			'autocomplete_options'  => $autocomplete_options,
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
		$options = array();
		$is_test = RekaiMain::get_instance()->get_test_mode();

		if ( $is_test ) {
			$options['advanced_mockdata'] = true;
			$options['projectid']         = get_option( 'rekai_project_id' ) ?? '';
			$options['srek']              = get_option( 'rekai_secret_key' ) ?? '';
		}

		return wp_json_encode( $options );
	}
}
