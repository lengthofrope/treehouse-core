<?php

/*
  Plugin Name: Treehouse CORE
  Plugin URI: https://github.com/lengthofrope/treehouse-core
  Description: Houses a lot of functionality used by other Treehouse components.
  Version: 0.0.1
  Author: LengthOfRope, Bas de Kort <bdekort@gmail.com>
  Author URI: https://github.com/lengthofrope
  License: MIT
  Text Domain: treehouse-core
  Domain Path: /languages
 */

defined('ABSPATH') or die();

// Set the folder of this plugin
if (!defined('TH_CORE_DIR')) {
    define('TH_CORE_VERSION', '0.0.1');
    define('TH_CORE_FILE', __FILE__);
    define('TH_CORE_DIR', plugin_dir_path(__FILE__));
    define('TH_CORE_DIR_REL', dirname(plugin_basename(__FILE__)));
}

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Silence is golden.';
    exit;
}


// Check wordpress version
if (isset($wp_version) && (version_compare($wp_version, '4.4.0', '<') || version_compare(PHP_VERSION, '5.3.0', '<'))) {
    // Output a nag error on admin interface
    add_action('admin_notices', function()
    {
        echo '<div class="error"><p>';
        echo 'Treehouse is enabled but not working properly since WP or PHP version requirements are not met. ';
        echo 'Treehouse <strong>requires</strong> at least <strong>WP 4.4.0</strong> and <strong>PHP 5.3.0</strong>.';
        echo '</p></div>';
    });

    // Make sure we do not continue
    return;
}

// Load the autoloader
require_once 'vendor/autoload.php';

// Load our application
if (class_exists('\LengthOfRope\Treehouse\Core')) {
    new \LengthOfRope\Treehouse\Core();
}
