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
		$handle = 'rekai-main';

		// The main Rek.ai script.
		wp_enqueue_script(
			$handle,
			$embed_code,
			array(),
			'1',
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

		if ( ! $this->should_load() ) {
			return;
		}
		$is_test = $this->get_test_mode();
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
}
