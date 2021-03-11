<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://BrianHenryIE.com
 * @since             1.0.0
 * @package           BH_WP_Plugin_Update_Git_Diffs
 *
 * @wordpress-plugin
 * Plugin Name:       Plugin Update Git Diffs
 * Plugin URI:        http://github.com/username/bh-wp-plugin-update-git-diffs/
 * Description:       When a plugin update becomes available, it is downloaded and a diff is run against the current code. A link to view it is added in the plugins.php update notification.
 * Version:           1.0.3
 * Author:            BrianHenryIE
 * Author URI:        https://BrianHenryIE.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bh-wp-plugin-update-git-diffs
 * Domain Path:       /languages
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\API;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\Settings;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\BrianHenryIE\WP_Logger\Logger;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'autoload.php';

/**
 * Current plugin version.
 */
define( 'BH_WP_PLUGIN_UPDATE_GIT_DIFFS_VERSION', '1.0.3' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function instantiate_bh_wp_plugin_update_git_diffs(): API {

	$settings = new Settings();
	$logger   = Logger::instance( $settings );

	$api = new API( $logger );

	new BH_WP_Plugin_Update_Git_Diffs( $api, $settings, $logger );

	return $api;
}

$GLOBALS['bh_wp_plugin_update_git_diffs'] = $bh_wp_plugin_update_git_diffs = instantiate_bh_wp_plugin_update_git_diffs();

