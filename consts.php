<?php
/**
 * Constants for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REKAI_PLUGIN_PATH', __DIR__ );
define( 'REKAI_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'REKAI_PLUGIN_VERSION', '1.9.11' );
