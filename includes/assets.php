<?php
/**
 * Registers the assets for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

// Register plugin styles and scripts.
// Can be enqueued by the plugin as needed.
add_action(
	'init',
	static function () {
		style_register(
			'rekai-frontend',
			'dist/css/index.css',
		);
		style_register(
			'rekai-admin',
			'dist/css/admin.css',
			array( 'wp-components' )
		);
		script_register(
			'rekai-backend',
			'dist/js/admin.js'
		);
	}
);

// This loads the Rek.ai Scripts.
add_action(
	'init',
	static function () {
		if ( ! get_option( 'rekai_project_id' ) ) {
			return;
		}
		$project_id = get_option( 'rekai_project_id' );
		$js_url     = sprintf( 'https://static.rekai.fi/%s.js', $project_id );
		// The main Rek.ai script.
		wp_enqueue_script(
			'rekai-main',
			$js_url,
			array(),
			'1',
			true
		);
	}
);
