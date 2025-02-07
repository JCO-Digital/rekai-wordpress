<?php
/**
 * Hooks for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

use Rekai\Options\OptionsPage;
use Rekai\Scripts\RekaiAutocomplete;
use Rekai\Scripts\RekaiMain;

// Initializes all classes.
add_action(
	'plugins_loaded',
	static function () {
		OptionsPage::get_instance();
		RekaiMain::get_instance();
		RekaiAutocomplete::get_instance();
	}
);

add_action(
	'init',
	'Rekai\register_plugin_assets'
);


add_action(
	'admin_notices',
	'Rekai\handle_notices'
);

add_filter( 'plugin_action_links_rekai-wordpress/rekai-wordpress.php', '\Rekai\settings_link' );
/**
 * Add settings link to plugin listing.
 *
 * @param array $links Array of plugin action links.
 * @return array Modified array of plugin action links.
 */
function settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url(
		add_query_arg(
			'page',
			'rekai-settings',
			get_admin_url() . 'admin.php'
		)
	);
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings', 'rekai-wordpress' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}
