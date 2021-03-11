<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/admin
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\Settings_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\Post;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/admin
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */
class Admin {

	/**
	 * The `get_plugin_version` setting we need here.
	 *
	 * @var Settings_Interface
	 */
	protected Settings_Interface $settings;

	/**
	 * The plugin logger.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $logger;

	/**
	 * Admin constructor.
	 *
	 * @param Settings_Interface $settings The plugin's settings.
	 * @param LoggerInterface    $logger The plugin's logger.
	 */
	public function __construct( Settings_Interface $settings, LoggerInterface $logger ) {
		$this->settings = $settings;
		$this->logger   = $logger;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @hooked admin_enqueue_scripts
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {

		wp_enqueue_style( 'bh-wp-plugin-update-git-diffs', plugin_dir_url( __FILE__ ) . 'css/bh-wp-plugin-update-git-diffs-admin.css', array(), $this->settings->get_plugin_version(), 'all' );
	}


	/**
	 * Register the invisible "submenu" used to display the diff page inside wp-admin.
	 *
	 * @hooked admin_menu
	 *
	 * Add a WordPress admin UI page, but without any menu linking to it.
	 *
	 * It's a URL that is not POST/PUTing, so nonce not necessary.
     * phpcs:disable WordPress.Security.NonceVerification.Recommended
	 */
	public function add_page(): void {

		$menu_slug   = Post::PLUGIN_UPDATE_GIT_DIFF_CPT;
		$parent_slug = '';
		// Hide the menu unless the page is open.
		if ( isset( $_GET['page'] ) && Post::PLUGIN_UPDATE_GIT_DIFF_CPT === $_GET['page'] ) {
			$parent_slug = 'plugins.php';
		}

		$capability = 'manage_options';

		add_submenu_page(
			$parent_slug,
			__( 'Diff', 'bh-wp-plugin-update-git-diffs' ),
			'',
			$capability,
			$menu_slug,
			array( $this, 'display_page' )
		);

	}

	/**
	 * Registered in @see add_page()
	 *
	 * It's a URL that is not POST/PUTing, so nonce not necessary.
     * phpcs:disable WordPress.Security.NonceVerification.Recommended
	 */
	public function display_page(): void {

		if ( ! isset( $_GET['id'] ) ) {

			return;
		}

		$post_id = intval( $_GET['id'] );

		$post = get_post( $post_id );

		if ( is_null( $post ) ) {
			$this->logger->warning( "Tried to get post id {$post_id}" );
			return;
		}

		$diff_strings = explode( "\n", $post->post_content );

		foreach ( $diff_strings as $line ) {

			$first_character = substr( $line, 0, 1 );

			$line = htmlspecialchars( $line );

			$css_classes = 'diff-line';

			switch ( $first_character ) {
				case '+':
					$css_classes .= ' delete';
					break;
				case '-':
					$css_classes .= ' add';
					break;
				default:
					$css_classes .= '';
			}

			$output = "<p class=\"{$css_classes}\">{$line}</p>";

			$allowed_html = array(
				'p' => array(
					'class' => array(),
				),
			);

			echo wp_kses( $output, $allowed_html );

		}

	}

}
