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
 */

namespace Rekai;

use Rekai\Options\OptionsPage;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/consts.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/assets.php';
require_once __DIR__ . '/includes/fields.php';

new OptionsPage();
