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
