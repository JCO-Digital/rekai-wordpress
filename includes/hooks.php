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

// Add filter to add settings link to plugin page.
add_filter(
	'plugin_action_links_rekai-wordpress/rekai-wordpress.php',
	array(
		OptionsPage::get_instance(),
		'settings_link',
	)
);
