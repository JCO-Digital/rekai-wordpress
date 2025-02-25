<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
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
	 * Returns a boolean whether the scripts/assets should be loaded.
	 * Checks against the Plugin settings.
	 *
	 * @return bool
	 */
	final public function should_load(): bool {
		$is_enabled = get_option( 'rekai_is_enabled' ) === '1';
		$embed_code = get_option( 'rekai_embed_code' );

		return ! ( ! $is_enabled || empty( $embed_code ) );
	}

	/**
	 * Checks whether the plugin is in test mode.
	 *
	 * This is determined by the environment type and the test mode setting.
	 * If the environment type is not production, the test mode is always true.
	 * Otherwise, the test mode is determined by the test mode setting.
	 *
	 * However, it can be overridden by the `rekai_override_test_mode` filter.
	 *
	 * @return bool
	 */
	final public function get_test_mode(): mixed {
		if ( wp_get_environment_type() !== 'production' ) {
			$is_test = true;
		} else {
			$is_test = get_option( 'rekai_test_mode' ) === '1';
		}

		/**
		 * Filters whether to override the test mode.
		 *
		 * @param bool $is_test Whether to override the test mode.
		 * @param string $environment_type The environment type.
		 * @param bool $is_test_default Whether the test mode is set to true in the settings.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( 'rekai_override_test_mode', $is_test, wp_get_environment_type(), get_option( 'rekai_test_mode' ) === '1' );
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
		$embed_code = get_option( 'rekai_embed_code' );
		if ( empty( $embed_code ) ) {
			return;
		}
		// The main Rek.ai script.
		wp_enqueue_script(
			'rekai-main',
			$embed_code,
			array(),
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
		$is_test = $this->get_test_mode();

		$is_admin = current_user_can( 'manage_options' );
		$data     = array(
			'is_admin' => $is_admin,
			'is_test'  => $is_test,
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template(
			'rekai-head',
			$data
		);
	}
}
