<?php
/**
 * Settings for the plugin and for the logger.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/API
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\API;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\BrianHenryIE\WP_Logger\API\Logger_Settings_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LogLevel;

/**
 * POJO. All strings. No get_option() even.
 *
 * Class Settings
 *
 * @package BrianHenryIE\WP_Plugin_Update_Git_Diffs\API
 */
class Settings implements Settings_Interface, Logger_Settings_Interface {

	/**
	 * TODO: This should be configurable.
	 *
	 * @see Logger_Settings_Interface
	 *
	 * @return string
	 */
	public function get_log_level(): string {
		return LogLevel::INFO;
	}

	/**
	 * Library bh-wp-logger uses the plugin name on its wp-admin log page. Supplying it here saves it the trouble of calculating it on each request.
	 *
	 * @see Logger_Settings_Interface
	 *
	 * @return string
	 */
	public function get_plugin_name(): string {
		return 'Plugin Update Git Diffs';
	}

	/**
	 * Library bh-wp-logger uses the plugin-slug. Supplying it here saves it the trouble of calculating it on each request.
	 *
	 * @see Logger_Settings_Interface
	 *
	 * @return string
	 */
	public function get_plugin_slug(): string {
		return 'bh-wp-plugin-update-git-diffs';
	}

	/**
	 * Library bh-wp-logger uses the basename. Supplying it here saves it the trouble of calculating it on each request.
	 *
	 * @see Logger_Settings_Interface
	 *
	 * @return string
	 */
	public function get_plugin_basename(): string {
		return 'bh-wp-plugin-update-git-diffs/bh-wp-plugin-update-git-diffs.php';
	}

	/**
	 * The plugin version, used in CSS file for caching.
	 *
	 * @return string
	 */
	public function get_plugin_version(): string {
		return '1.0.3';
	}
}
