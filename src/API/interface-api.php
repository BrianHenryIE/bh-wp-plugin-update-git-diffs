<?php
/**
 * Primary functions to use widely in the plugin.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/API
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\API;

use stdClass;
use WP_Post;

interface API_Interface {

	/**
	 * When WordPress saves available plugin update information to transients, this function should be called with the
	 * data for each update.
	 *
	 * @see Update::on_save_plugin_update_data()
	 *
	 * @param stdClass $update The update data as determined by WordPress in update.php.
	 */
	public function process_available_update( stdClass $update ): void;

	/**
	 * Returns, if already existing, a WP_Post containing the diff between two version of a plugin.
	 *
	 * If the diff does not exist, null is returned. No attempt is made to generate it in this function.
	 *
	 * @param string $plugin_slug Plugin whose diff we're interested in.
	 * @param string $from_version Earlier version.
	 * @param string $to_version Later version.
	 * @return ?WP_Post
	 */
	public function get_diff_post_for_update( string $plugin_slug, string $from_version, string $to_version ): ?WP_Post;

}
