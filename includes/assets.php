<?php
/**
 * Registers the assets for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

/**
 * Handles registering the plugin styles and scripts.
 * These can be enqueued by the plugin as needed.
 *
 * @return void
 */
function register_plugin_assets(): void {
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

/**
 * Handles loading the Rek.ai scripts.
 *
 * @return void
 */
function load_rekai_scripts(): void {
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

/**
 * Handles rendering the Rek.ai scripts.
 *
 * @return void
 */
function render_rekai_scripts(): void {
	if ( ! get_option( 'rekai_project_id' ) ) {
		return;
	}
	$project_id = get_option( 'rekai_project_id' );
	$is_admin   = current_user_can( 'manage_options' );

	$data = array(
		'project_id' => $project_id,
		'is_admin'   => $is_admin,
	);

	echo render_template(
		'rekai-head',
		$data
	);
}
