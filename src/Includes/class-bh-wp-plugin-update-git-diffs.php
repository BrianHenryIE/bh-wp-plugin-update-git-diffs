<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * frontend-facing side of the site and the admin area.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/includes
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin\Admin;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin\Plugin_List_Page;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\API_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\Settings_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * frontend-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/includes
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class BH_WP_Plugin_Update_Git_Diffs {

	/**
	 * The primary plugin functions.
	 *
	 * @var API_Interface
	 */
	protected API_Interface $api;

	/**
	 * The plugin settings, as used in the Admin class.
	 *
	 * @var Settings_Interface
	 */
	protected Settings_Interface $settings;

	/**
	 * PSR logger.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $logger;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the frontend-facing side of the site.
	 *
	 * @param API_Interface      $api The plugin API.
	 * @param Settings_Interface $settings The plugin settings.
	 * @param LoggerInterface    $logger The plugin logger.
	 * @since    1.0.0
	 */
	public function __construct( API_Interface $api, Settings_Interface $settings, LoggerInterface $logger ) {

		$this->api      = $api;
		$this->settings = $settings;
		$this->logger   = $logger;

		$this->set_locale();
		$this->define_admin_hooks();

		$this->define_plugin_update_hooks();

		$this->define_post_hooks();
		$this->define_plugin_list_page_hooks();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	protected function set_locale(): void {

		$plugin_i18n = new I18n();

		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	protected function define_admin_hooks(): void {

		$plugin_admin = new Admin( $this->settings, $this->logger );

		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );

		add_action( 'admin_menu', array( $plugin_admin, 'add_page' ) );

	}

	/**
	 * Register a custom post type to save the generated diffs.
	 * Hook into get_post_permalink() to return an admin url for the diffs.
	 */
	protected function define_post_hooks(): void {

		$post = new Post( $this->logger );

		add_action( 'init', array( $post, 'register_git_diff_post_type' ) );

		add_action( 'post_type_link', array( $post, 'get_permalink' ), 10, 4 );
	}

	/**
	 * When wp-includes/update.php checks for available plugin updates, hook into it saving data in a transient to
	 * process that data.
	 */
	protected function define_plugin_update_hooks(): void {

		$update = new Update( $this->api, $this->logger );

		add_action( 'set_site_transient_update_plugins', array( $update, 'on_save_plugin_update_data' ), 10, 3 );

	}

	/**
	 * Add an action for each entry on plugins.php to add a link to any generated diff for that plugin.
	 */
	protected function define_plugin_list_page_hooks(): void {

		$plugin_list_page = new Plugin_List_Page( $this->api, $this->logger );

		global $pagenow;

		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		foreach ( array_keys( get_plugins() ) as $file ) {
			add_action( "in_plugin_update_message-{$file}", array( $plugin_list_page, 'print_link_in_plugin_update_message' ), 10, 2 );
		}

	}

}
