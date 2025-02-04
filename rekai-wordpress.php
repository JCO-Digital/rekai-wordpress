<?php
/**
 * Plugin Name: Rekai WordPress
 * Plugin URI: https://github.com/jco-digital/rekai-wordpress
 * Description: Rek.ai integration for WordPress
 * Version: 0.1.0
 * Author: J&Co Digital Oy
 * Author URI: https://jco.fi
 * Domain Path: /languages
 * Text Domain: rekai-wordpress
 *
 * @package Rekai
 */

namespace Rekai;

// Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Constants for the plugin.
require_once __DIR__ . '/consts.php';

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
require_once __DIR__ . '/includes/block-functions.php';
