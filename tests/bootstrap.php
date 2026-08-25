<?php
/**
 * PHPUnit bootstrap for the rek-ai plugin's unit tests.
 *
 * These are plain unit tests (via Brain Monkey), not WordPress integration tests: no
 * WordPress core, database, or wp-phpunit test suite is loaded. WordPress functions used
 * by the code under test are stubbed per-test in tests/TestCase.php.
 *
 * @package Rekai
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	// Plugin files all guard on this; any non-empty path is fine for unit tests.
	define( 'ABSPATH', __DIR__ . '/' );
}

// WordPress core time constants (wp-includes/default-constants.php), referenced directly by
// the code under test; not loaded here since these are plain unit tests, not WP integration
// tests.
defined( 'MINUTE_IN_SECONDS' ) || define( 'MINUTE_IN_SECONDS', 60 );
defined( 'DAY_IN_SECONDS' ) || define( 'DAY_IN_SECONDS', 86400 );

$plugin_root = dirname( __DIR__ );

require_once $plugin_root . '/classes/class-singleton.php';
require_once $plugin_root . '/classes/scripts/class-rekaibase.php';
require_once $plugin_root . '/classes/scripts/class-rekaimain.php';
require_once $plugin_root . '/includes/helpers.php';
require_once $plugin_root . '/includes/attribute-helpers.php';
require_once $plugin_root . '/includes/ssr.php';
