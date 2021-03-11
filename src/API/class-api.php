<?php
/**
 * The heavy-lifting of the plugin.
 *
 * Runs a Git diff against two directories.
 * Downloads a plugin update, runs the diff, and saves the output to a post.
 *
 * @link       https://BrianHenryIE.com
 * @since      1.0.0
 *
 * @package    BH_WP_Plugin_Update_Git_Diffs
 * @subpackage BH_WP_Plugin_Update_Git_Diffs/API
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\API;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\SebastianBergmann\Diff\Differ;
use Exception;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\Post;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\LoggerInterface;
use Plugin_Upgrader;
use stdClass;
use WP_Ajax_Upgrader_Skin;
use WP_Post;

/**
 * Uses internal WordPress functions to download plugin updates.
 * Uses PHP's exec to run git diff on the existing and updated plugin, then saves the result in a cpt.
 *
 * Class API
 *
 * @package BrianHenryIE\WP_Plugin_Update_Git_Diffs\API
 */
class API implements API_Interface {

	/**
	 * PSR logger.
	 *
	 * @var LoggerInterface
	 */
	protected LoggerInterface $logger;

	/**
	 * API constructor.
	 *
	 * @param LoggerInterface $logger PSR logger.
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Check for an existing post for the specified slug two versions.
	 *
	 * @param string $plugin_slug The plugin's slug.
	 * @param string $from_version The prior plugin/existing version.
	 * @param string $to_version The updated plugin version.
	 * @return ?WP_Post
	 */
	public function get_diff_post_for_update( string $plugin_slug, string $from_version, string $to_version ): ?WP_Post {

		$post_name = $this->get_diff_post_name_for_update( $plugin_slug, $from_version, $to_version );

		$post = get_page_by_title( $post_name, OBJECT, Post::PLUGIN_UPDATE_GIT_DIFF_CPT );

		/* @phpstan-ignore-next-line We know we're not returning an array, but PHPStan does not. */
		return $post;
	}

	/**
	 * Generate the post name string from the slug and two versions.
	 *
	 * @param string $plugin_slug The plugin's slug.
	 * @param string $from_version The prior plugin/existing version.
	 * @param string $to_version The updated plugin version.
	 * @return string
	 */
	public function get_diff_post_name_for_update( string $plugin_slug, string $from_version, string $to_version ): string {

		$post_name = "{$plugin_slug}-v{$from_version}-v{$to_version}";

		return $post_name;
	}

	/**
	 * Takes a plugin 'update' details object from WordPress,
	 * checks has it already been processed,
	 * downloads the update,
	 * runs the diff,
	 * saves the diff.
	 *
	 * @see wp_ajax_install_plugin()
	 * @see Plugin_Upgrader
	 *
	 * @param stdClass $update The update data, partial data from what WordPress is storing in its transient.
	 */
	public function process_available_update( stdClass $update ): void {

		$plugin_basename = $update->plugin;
		$plugin_slug     = $update->slug;

		$existing_plugin_dir = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_slug;

		$existing_plugin_data    = get_plugins()[ $plugin_basename ];
		$existing_plugin_version = $existing_plugin_data['Version'];

		$new_plugin_version = $update->new_version;

		$post_name = $this->get_diff_post_name_for_update( $plugin_slug, $existing_plugin_version, $new_plugin_version );

		$post = $this->get_diff_post_for_update( $plugin_slug, $existing_plugin_version, $new_plugin_version );

		if ( ! is_null( $post ) ) {
			$this->logger->debug( "Post already exists for {$post_name}. Returning." );
			return;
		}

		$update_package_url = $update->package;

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$downloaded_update_file_path = $upgrader->download_package( $update_package_url );

		if ( is_wp_error( $downloaded_update_file_path ) ) {
			$this->logger->error( $downloaded_update_file_path->get_error_message(), array( 'error' => $downloaded_update_file_path ) );
			return;
		}

		// In case a premium plugin site limits downloads.
		// TBH, I'm not sure it will be used again when WordPress tries to download again.
		// Some filter can probably be added to return the file path on subsequent tries.

		// Needs to be initialized before it is used inside unpack_package().
		WP_Filesystem();

		$delete_package            = false;
		$extracted_update_dir_path = $upgrader->unpack_package( $downloaded_update_file_path, $delete_package );

		if ( is_wp_error( $extracted_update_dir_path ) ) {
			$this->logger->error( $extracted_update_dir_path->get_error_message(), array( 'wp_error' => $extracted_update_dir_path ) );
			return;
		}

		$extracted_update_dir_path = $extracted_update_dir_path . DIRECTORY_SEPARATOR . $plugin_slug;

		try {
            $diff_string = $this->diff_dirs($existing_plugin_dir, $extracted_update_dir_path);
        } catch( \Exception $e ) {
		    $this->logger->error( $e->getMessage() );
		    return;
        }

		$post_data = array(
			'post_content'          => wp_slash( $diff_string ),
			'post_content_filtered' => '',
			'post_title'            => $post_name,
			'post_excerpt'          => '',
			'post_status'           => 'publish',
			'post_type'             => Post::PLUGIN_UPDATE_GIT_DIFF_CPT,
			'meta_input'            => array(
				'existing_plugin_data' => $existing_plugin_data,
				'update'               => $update,
			),
		);

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			$this->logger->error( $post_id->get_error_message(), array( 'wp_error' => $post_id ) );
			return;
		}

		$this->logger->info( "Added post {$post_name}" );
	}

//	public function dfff( $a, $b ) {
//
//        $differ = new Differ;
//
//        // Dir list both to create full
//
//
//
//        print $differ->diff('foo', 'bar');
//    }

	/**
	 * Run exec() to run `git diff` on the two directories specified.
	 * Returns the diff as a single string, with any file paths mentioned shorted to remove ABSPATH and the temp path.
	 *
	 * TODO: The plugin should check that exec can be used and that git is present on the server.
	 * "PHP system calls are often disabled by server admins."
	 *
	 * @see exec()
	 * @see https://stackoverflow.com/a/54335167/336146
	 *
	 * @param string $absolute_dir_path_1 First directory to compare, presumably the existing plugin.
	 * @param string $absolute_dir_path_2 Second directory to compare, presumably the recently downloaded update.
	 *
	 * @return string
	 *
	 * @throws \Exception If exec cannot be called, if Git executable does not exist, or if the it command exits unsuccessfully.
	 */
	public function diff_dirs( string $absolute_dir_path_1, string $absolute_dir_path_2 ): string {

		if ( ! $this->exec_enabled() ) {
			throw new Exception( 'PHP exec not available on this server' );
		}

		// -G. ignores file mode changes (chmod.644/755).
        $diff_cmd = "git diff --no-index -G. {$absolute_dir_path_1} {$absolute_dir_path_2}";

        $diff_cmd = "/usr/bin/git diff --no-index -G. {$absolute_dir_path_1} {$absolute_dir_path_2}";



		$output = array();

		/* phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.system_calls_exec */
//		$result = exec( $diff_cmd, $output, $result_code );

        $output = $this->run( $diff_cmd );

//		/* @phpstan-ignore-next-line php.com documentation says exec can return false but the PhpDoc does not. */
//		if ( false === $result ) {
//			$message = 'PHP exec failed';
//			$this->logger->error( $message, array( 'exec' => $diff_cmd ) );
//			throw new Exception( $message );
//		}
//
//		// Presumably this won't be reached when there's a real error.
//		if ( 0 !== $result_code && 1 !== $result_code ) {
//			$message = "PHP exec return with exit code {$result_code}";
//			$this->logger->error( $message, array( 'exec' => $diff_cmd ) );
//			throw new Exception( $message );
//		}

//		$this->logger->info( "Executed `{$diff_cmd}` which returned with exit code {$result_code}" );

//		$diff_string = implode( "\n", $output );

        $diff_string = $output;

		// Remove the absolute directory paths, since they're mostly redundant and don't add any useful information.
		$diff_string = str_replace( $absolute_dir_path_2 . DIRECTORY_SEPARATOR, '', $diff_string );
		$diff_string = str_replace( ABSPATH, '', $diff_string );

		return $diff_string;
	}

	/**
	 * Determine the PHP `exec` function available on this server.
	 *
	 * @see https://stackoverflow.com/questions/3938120/check-if-exec-is-disabled
	 * @see https://www.php.net/manual/en/function.exec.php
	 *
	 * @return bool
	 */
	protected function exec_enabled(): bool {
		$disabled_functions = ini_get( 'disable_functions' );
		if ( ! is_string( $disabled_functions ) ) {
			return function_exists( 'exec' );
		}
		$disabled = explode( ',', $disabled_functions );
		return function_exists( 'exec' ) && ! in_array( 'exec', $disabled, true );
	}

	// https://stackoverflow.com/questions/34778191/run-git-push-origin-master-commands-using-php
    private function run($command, array $arguments = array()) {
        $pipes = array();
        $descriptorspec = array(
            array('pipe', 'r'),  // STDIN
            array('pipe', 'w'),  // STDOUT
            array('file', get_temp_dir() . 'error.txt', 'w'),  // STDERR
        );
        $process = proc_open($command, $descriptorspec, $pipes);
        foreach ($arguments as $arg) {
            // Write each of the supplied arguments to STDIN
            fwrite($pipes[0], (preg_match("/\n(:?\s+)?$/", $arg) ? $arg : "{$arg}\n"));
        }
        $response = stream_get_contents($pipes[1]);
        // Make sure that each pipe is closed to prevent a lockout
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        proc_close($process);
        return $response;
    }

}
