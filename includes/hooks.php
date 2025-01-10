<?php
/**
 * Hooks for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

use Rekai\Options\OptionsPage;

// Initializes all classes.
add_action(
	'plugins_loaded',
	static function () {
		new OptionsPage();
	}
);

add_action(
	'init',
	'Rekai\register_plugin_assets'
);

// This loads the Rek.ai Scripts.
add_action(
	'wp_enqueue_scripts',
	'Rekai\load_rekai_scripts'
);

// This renders the Rek.ai inline scripts.
add_action(
	'wp_head',
	'Rekai\render_rekai_scripts'
);

add_action(
	'admin_notices',
	'Rekai\handle_notices'
);
