<?php
/**
 * When WordPress's update.php changes the site transient for available plugin updates, process it.
 *
 * I.e. when it finds new plugin updates, send them to the API class to process.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/Includes
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\API_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;

/**
 * Hooks into transient update `set_site_transient_update_plugins`, i.e. for transient `update_plugins`, then
 * sends each available update to API to process.
 *
 * @see wp_update_plugins()
 * @see wp-includes/update.php
 *
 * Class Update
 * @package BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes
 */
class Update {

	/**
	 * The primary plugin functions.
	 *
	 * @var API_Interface
	 */
	protected API_Interface $api;

	/**
	 * PSR logger.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $logger;

	/**
	 * Update constructor.
	 *
	 * @param API_Interface   $api The plugin's main functions.
	 * @param LoggerInterface $logger The plugin's logger.
	 */
	public function __construct( API_Interface $api, LoggerInterface $logger ) {
		$this->api    = $api;
		$this->logger = $logger;
	}

	/**
	 * After the list of plugins needing updates changes, download the updated plugin and record the diff.
	 *
	 * @hooked set_site_transient_update_plugins
	 *
	 * @see wp_update_plugins()
	 * @see set_site_transient()
	 *
	 * @param mixed  $value      Site transient value.
	 * @param int    $expiration Time until expiration in seconds.
	 * @param string $transient  Transient name.
	 */
	public function on_save_plugin_update_data( $value, int $expiration, string $transient ): void {

		if ( isset( $value->response ) ) {
			foreach ( $value->response as $update ) {

				$this->api->process_available_update( $update );

			}
		}

	}
}
