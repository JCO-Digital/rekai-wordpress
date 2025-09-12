<?php
/**
 * Load all classes.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/classes/class-singleton.php';
require_once __DIR__ . '/classes/options/class-optionspage.php';
require_once __DIR__ . '/classes/scripts/class-rekaibase.php';
require_once __DIR__ . '/classes/scripts/class-rekaimain.php';
require_once __DIR__ . '/classes/scripts/class-rekaiautocomplete.php';
