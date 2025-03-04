<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Handles the Rek.ai scripts.
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
