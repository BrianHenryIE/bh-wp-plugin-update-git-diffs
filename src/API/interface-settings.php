<?php
/**
 * Variables that other classes should not explicitly need to know.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/API
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\API;

interface Settings_Interface {

	/**
	 * The plugin version, used for CSS versioning.
	 *
	 * @return string The current plugin version.
	 */
	public function get_plugin_version(): string;

}
