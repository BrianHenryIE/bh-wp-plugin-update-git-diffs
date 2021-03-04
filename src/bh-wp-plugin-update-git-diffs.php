<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           BH_WP_Plugin_Update_Git_Diffs
 *
 * @wordpress-plugin
 * Plugin Name:       BH WP Plugin Update Git Diffs
 * Plugin URI:        http://github.com/username/bh-wp-plugin-update-git-diffs/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            BrianHenryIE
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-plugin-update-git-diffs
 * Domain Path:       /languages
 */

namespace BH_WP_Plugin_Update_Git_Diffs;

use BH_WP_Plugin_Update_Git_Diffs\Includes\Activator;
use BH_WP_Plugin_Update_Git_Diffs\Includes\Deactivator;
use BH_WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BH_WP_PLUGIN_UPDATE_GIT_DIFFS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-activator.php
 */
function activate_bh_wp_plugin_update_git_diffs(): void {

	Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-deactivator.php
 */
function deactivate_bh_wp_plugin_update_git_diffs(): void {

	Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'BH_WP_Plugin_Update_Git_Diffs\activate_bh_wp_plugin_update_git_diffs' );
register_deactivation_hook( __FILE__, 'BH_WP_Plugin_Update_Git_Diffs\deactivate_bh_wp_plugin_update_git_diffs' );


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_plugin_update_git_diffs(): BH_WP_Plugin_Update_Git_Diffs {

	$plugin = new BH_WP_Plugin_Update_Git_Diffs();

	return $plugin;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and frontend-facing site hooks.
 */
$GLOBALS['bh_wp_plugin_update_git_diffs'] = $bh_wp_plugin_update_git_diffs = instantiate_bh_wp_plugin_update_git_diffs();
$bh_wp_plugin_update_git_diffs->run();
