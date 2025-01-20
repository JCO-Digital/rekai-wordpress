<?php
/**
 * Handles the Rek.ai scripts.
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
class RekaiMain extends Singleton {

	/**
	 * Initializes the script loading.
	 */
	protected function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_head', array( $this, 'render_head' ) );
	}

	/**
	 * Returns a boolean wether the scripts/assets should be loaded.
	 * Checks against the Plugin settings.
	 *
	 * @return bool
	 */
	final public function should_load(): bool {
		$is_enabled = get_option( 'rekai_is_enabled' ) === '1';
		$script_key = get_option( 'rekai_script_key' );
		return ! ( ! $is_enabled || empty( $script_key ) );
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
		$script_key           = get_option( 'rekai_script_key' );
		$autocomplete_enabled = get_option( 'rekai_autocomplete_enabled' );
		$js_url               = sprintf( 'https://static.rekai.fi/%s.js', $script_key );
		// The main Rek.ai script.
		wp_enqueue_script(
			'rekai-main',
			$js_url,
			array(),
			'1',
			true
		);
		if ( $autocomplete_enabled === '1' ) {
			wp_enqueue_script(
				'rekai-autocomplete',
				'https://static.rekai.se/addon/v3/rekai_autocomplete.min.js',
				array( 'rekai-main' ),
				'1',
				true
			);
		}
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
		$script_key = get_option( 'rekai_script_key' );
		$is_test    = get_option( 'rekai_test_mode' ) === '1';

		$is_admin = current_user_can( 'manage_options' );
		$data     = array(
			'script_key' => $script_key,
			'is_admin'   => $is_admin,
			'is_test'    => $is_test,
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template(
			'rekai-head',
			$data
		);
	}
}
