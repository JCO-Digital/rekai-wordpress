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
		$project_id = get_option( 'rekai_project_id' );
		return ! ( ! $is_enabled || empty( $project_id ) );
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
		$project_id = get_option( 'rekai_project_id' );

		$js_url = sprintf( 'https://static.rekai.fi/%s.js', $project_id );
		// The main Rek.ai script.
		wp_enqueue_script(
			'rekai-main',
			$js_url,
			array(),
			'1',
			true
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
		$project_id = get_option( 'rekai_project_id' );

		$is_admin = current_user_can( 'manage_options' );
		$data     = array(
			'project_id' => $project_id,
			'is_admin'   => $is_admin,
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template(
			'rekai-head',
			$data
		);
	}
}
