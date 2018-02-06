<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/oysteink/
 * @since             1.0.0
 * @package           Cge
 *
 * @wordpress-plugin
 * Plugin Name:       Card Game Engine
 * Plugin URI:        https://github.com/oysteink/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            oysteink
 * Author URI:        https://github.com/oysteink/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cge
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cge-activator.php
 */
function activate_cge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cge-activator.php';
	Cge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cge-deactivator.php
 */
function deactivate_cge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cge-deactivator.php';
	Cge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cge' );
register_deactivation_hook( __FILE__, 'deactivate_cge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cge.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cge() {

	$plugin = new Cge();
	$plugin->run();

}
run_cge();
