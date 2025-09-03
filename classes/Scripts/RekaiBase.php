<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Handles the Rek.ai scripts.
 *
 * @package Rekai
 */

namespace Rekai\Scripts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rekai\Singleton;

/**
 * Rek.ai base class for script loading.
 *
 * @since 0.1.0
 */
abstract class RekaiBase extends Singleton {

	/**
	 * The default static URL for Rek.ai resources.
	 *
	 * @var string
	 */
	const DEFAULT_URL = 'https://static.rekai.se';

	/**
	 * Initializes the script loading.
	 */
	protected function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Handles equeue of the Rek.ai scripts.
	 *
	 * @return void
	 */
	abstract public function enqueue();

	/**
	 * Returns a boolean whether the scripts/assets should be loaded.
	 *
	 * @return bool Whether the scripts/assets should be loaded.
	 */
	public function should_load(): bool {
		if (
			get_option( 'rekai_is_enabled' ) !== '1'
			|| empty( get_option( 'rekai_embed_code' ) )
			|| $this->is_incomplete_test_mode()
		) {
			return false;
		}

		return true;
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
	public function get_test_mode(): mixed {
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
	 * Checks whether the plugin is in test mode but the project ID or secret key is missing.
	 *
	 * @return bool
	 */
	public function is_incomplete_test_mode(): bool {
		if ( ! $this->get_test_mode() ) {
			// Not in test mode.
			return false;
		}
		if ( in_array( wp_get_environment_type(), array( 'production', 'local' ) ) ) {
			// Allow test mode without id / secret for local and production, since it probably works there.
			return false;
		}
		if ( empty( get_option( 'rekai_project_id', '' ) ) ) {
			// Invalid without ID.
			return true;
		}
		if ( empty( get_option( 'rekai_secret_key', '' ) ) ) {
			// Invalid without secret.
			return true;
		}
		return false;
	}

	/**
	 * Gets the base URL from the embed code as to not use different base URLs for CSP reasons.
	 *
	 * This method takes a URL path, appends it to the static URL base, and returns
	 * the complete URL. If an embed code exists and contains a valid static URL pattern,
	 * that URL will be used as the base instead of the default one.
	 *
	 * @param string $url The URL path to append to the static URL base.
	 * @return string The complete static URL.
	 */
	protected function get_static_url( string $url ): string {
		$embed_code = get_option( 'rekai_embed_code', '' );
		if ( ! empty( $embed_code ) && preg_match( '/^https:\/\/static\.[^\/]+/', $embed_code, $matches ) ) {
			return $matches[0] . $url;
		}
		return self::DEFAULT_URL . $url;
	}
}
