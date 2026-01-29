<?php
/**
 * Handles the Rek.ai scripts.
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
class RekaiMain extends RekaiBase {

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
		if ( ! filter_var( $embed_code, FILTER_VALIDATE_URL ) || ! str_starts_with( $embed_code, 'https://' ) ) {
			return;
		}

		$handle = 'rekai-main';

		add_filter( 'script_loader_tag', array( $this, 'add_data_attributes' ), 10, 2 );

		// The main Rek.ai script.
		wp_enqueue_script(
			$handle,
			$embed_code,
			array(),
			REKAI_PLUGIN_VERSION,
			false
		);

		$this->create_inline( $handle );
	}

	/**
	 * Creates an inline script block rek.ai view saving.
	 *
	 * Block view saves if the user is either an administrator or test mode is enabled.
	 *
	 * @param string $handle The script handle to attach the inline script to.
	 * @return void
	 */
	final public function create_inline( $handle ): void {
		$is_test  = $this->get_test_mode();
		$is_admin = current_user_can( 'manage_options' );
		if ( ! $is_admin && ! $is_test ) {
			return;
		}

		wp_add_inline_script(
			$handle,
			'window.rek_blocksaveview = true;',
			'after'
		);
	}


	/**
	 * Adds data attributes to the Rek.ai script tag.
	 *
	 * Adds the data-useconsent attribute to the script tag if consent mode is enabled.
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string The modified script tag.
	 */
	final public function add_data_attributes( $tag, $handle ): string {
		if ( 'rekai-main' !== $handle ) {
			return $tag;
		}
		if ( '1' === get_option( 'rekai_consent_mode' ) ) {
			return str_replace(
				'src=',
				'data-useconsent="true" src=',
				$tag
			);
		}
		return $tag;
	}
}
