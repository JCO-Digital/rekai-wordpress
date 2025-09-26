<?php
/**
 * Plugin Name: Rek.ai
 * Plugin URI: https://docs.rek.ai/integration-modules/wordpress
 * Description: Rek.ai integration for WordPress
 * Version: 1.9.4
 * Author: Rek.ai
 * Author URI: https://rek.ai
 * Domain Path: /languages
 * Text Domain: rek-ai
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Constants for the plugin.
require_once __DIR__ . '/consts.php';

// Class Loader.
require_once __DIR__ . '/classloader.php';

use Rekai\Options\OptionsPage;

// Helper functions for the plugin.
require_once __DIR__ . '/includes/helpers.php';

// Plugin notices.
require_once __DIR__ . '/includes/notices.php';

// Generic handlers for different field types.
require_once __DIR__ . '/includes/fields.php';

// Handles registering the plugin styles and scripts.
require_once __DIR__ . '/includes/assets.php';

// All hooks of the plugin are defined here.
require_once __DIR__ . '/includes/hooks.php';

// Gutenberg blocks.
require_once __DIR__ . '/includes/blocks.php';

// Block specific functions.
require_once __DIR__ . '/includes/attribute-helpers.php';

// REST API.
require_once __DIR__ . '/includes/rest-api.php';

// Shortcodes.
require_once __DIR__ . '/includes/shortcode.php';

/**
 * Run when activating the Rekai plugin and enabling autoload for specific plugin options.
 *
 * @return void
 */
function rekai_activate(): void {
	wp_set_options_autoload(
		OptionsPage::$autoload_options,
		true
	);
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\rekai_activate' );

/**
 * Run when deactivating the Rekai plugin and disabling autoload for specific plugin options.
 *
 * @return void
 */
function rekai_deactivate(): void {
	wp_set_options_autoload(
		OptionsPage::$autoload_options,
		false
	);
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\rekai_deactivate' );
