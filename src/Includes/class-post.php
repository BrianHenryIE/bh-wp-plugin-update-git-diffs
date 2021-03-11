<?php
/**
 * Register a custom post type for storing the diffs.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/Includes
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;
use WP_Post;

/**
 * Uses register_post_type to ...
 * Filters get_post_permalink to return an admin url for the post.
 *
 * Class Post
 *
 * @package BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes
 */
class Post {

	const PLUGIN_UPDATE_GIT_DIFF_CPT = 'plugin_update_diff';

	/**
	 * PSR logger.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $logger;

	/**
	 * Post constructor.
	 *
	 * @param LoggerInterface $logger PSR logger.
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Add the custom post type `plugin_update_diff` to WordPress.
	 *
	 * @see register_post_type()
	 *
	 * @hooked init
	 */
	public function register_git_diff_post_type(): void {

		$args = array(
			'labels'      => array(
				'name'          => __( 'Git Diffs', 'bh-wp-plugin-update-git-diffs' ),
				'singular_name' => __( 'Git Diff', 'bh-wp-plugin-update-git-diffs' ),
			),
			'public'      => false,
			'has_archive' => false,
			'description' => 'Holds git diffs for plugin updates',

		);

		$post_type_name = self::PLUGIN_UPDATE_GIT_DIFF_CPT;

		$post_type = register_post_type( $post_type_name, $args );

		if ( is_wp_error( $post_type ) ) {
			$this->logger->error( $post_type->get_error_message(), array( 'wp_error' => $post_type ) );
		}
	}

	/**
	 * Return `admin.php?page=plugin_update_diff&id=123` for post permalinks links.
	 *
	 * @hooked post_type_link
	 * @see get_post_permalink()
	 *
	 * @param string  $post_link The post's already generated and filtered permalink.
	 * @param WP_Post $post      The post in question.
	 * @param bool    $leavename Whether to keep the post name.
	 * @param bool    $sample    Is it a sample permalink.
	 *
	 * @return string
	 */
	public function get_permalink( string $post_link, WP_Post $post, bool $leavename, bool $sample ): string {

		if ( self::PLUGIN_UPDATE_GIT_DIFF_CPT !== $post->post_type ) {
			return $post_link;
		}

		$page = self::PLUGIN_UPDATE_GIT_DIFF_CPT;

		return admin_url( "admin.php?page={$page}&id={$post->ID}" );

	}
}
