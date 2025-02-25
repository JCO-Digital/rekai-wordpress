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
		$is_test           = RekaiMain::get_instance()->get_test_mode();
		$options['params'] = array();

		if ( $is_test ) {
			$options['advanced_mockdata'] = true;
			$options['projectid']         = get_option( 'rekai_project_id' ) ?? '';
			$options['srek']              = get_option( 'rekai_secret_key' ) ?? '';
		}

		$options['params']['nrofhits'] = (int) get_option( 'rekai_autocomplete_nrofhits', 10 );

		if ( '1' === get_option( 'rekai_autocomplete_usecurrentlang' ) ) {
			$options['params']['allowedlangs'] = get_locale();
		}

		return wp_json_encode( $options );
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
	private function get_static_url( string $url ): string {
		$embed_code = get_option( 'rekai_embed_code', '' );
		if ( ! empty( $embed_code ) && preg_match( '/^https:\/\/static\.[^\/]+/', $embed_code, $matches ) ) {
			return $matches[0] . $url;
		}
		return self::DEFAULT_URL . $url;
	}
}
