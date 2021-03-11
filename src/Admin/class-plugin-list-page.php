<?php
/**
 * Changes on wp-admin/plugins.php.
 *
 * Prints a simple anchor link to generated diffs in the yellow update notifications on plugins.php
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/admin
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\API_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;
use stdClass;

/**
 * The hardest thing this class does is sanitize before echo.
 *
 * @see wp-admin/plugin-install.php
 * @see WP_Plugin_Install_List_Table
 * @see wp_plugin_update_row()
 *
 * Class Plugin_List_Page
 * @package BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin
 */
class Plugin_List_Page {

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
	 * Plugin_List_Page constructor.
	 *
	 * @param API_Interface   $api The plugin's main functions.
	 * @param LoggerInterface $logger The plugin logger.
	 */
	public function __construct( API_Interface $api, LoggerInterface $logger ) {
		$this->api    = $api;
		$this->logger = $logger;
	}

	/**
	 * Prints a simple link to the relevant diff. Hooked to run inside the "update available" yellow notification
	 * on plugins.php.
	 *
	 * @hooked in_plugin_update_message-{$file}
	 *
	 * NB: WordPress PhpDoc says the second parameter is an array, but PHP says it's an object.
	 *
	 * @param array<string, mixed> $plugin_data {
	 *     An array of plugin metadata.
	 *
	 *     @type string $name        The human-readable name of the plugin.
	 *     @type string $plugin_uri  Plugin URI.
	 *     @type string $version     Plugin version.
	 *     @type string $description Plugin description.
	 *     @type string $author      Plugin author.
	 *     @type string $author_uri  Plugin author URI.
	 *     @type string $text_domain Plugin text domain.
	 *     @type string $domain_path Relative path to the plugin's .mo file(s).
	 *     @type bool   $network     Whether the plugin can only be activated network wide.
	 *     @type string $title       The human-readable title of the plugin.
	 *     @type string $author_name Plugin author's name.
	 *     @type bool   $update      Whether there's an available update. Default null.
	 * }
	 * @param stdClass             $response {
	 *                 An array of metadata about the available plugin update.
	 *
	 *     @type int    $id          Plugin ID.
	 *     @type string $slug        Plugin slug.
	 *     @type string $new_version New plugin version.
	 *     @type string $url         Plugin URL.
	 *     @type string $package     Plugin update package URL.
	 * }
	 */
	public function print_link_in_plugin_update_message( array $plugin_data, stdClass $response ): void {

		$post = $this->api->get_diff_post_for_update( $response->slug, $plugin_data['Version'], $response->new_version );

		if ( is_null( $post ) ) {
			return;
		}

		$url = get_permalink( $post );

		$output = "&nbsp;<a href=\"{$url}\">View diff</a>.";

		$allowed_html = array(
			'a' => array(
				'href'  => array(),
				'title' => array(),
			),
		);

		echo wp_kses( $output, $allowed_html );

	}

}
