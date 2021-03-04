<?php
/**
 * Tests for the root plugin file.
 *
 * @package BH_WP_Plugin_Update_Git_Diffs
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BH_WP_Plugin_Update_Git_Diffs;

use BH_WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs;

/**
 * Class Plugin_WP_Mock_Test
 *
 * @coversNothing
 */
class Plugin_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// This is required for `'times' => 1` to be verified.
	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}
	
	/**
	 * Verifies the plugin initialization.
	 */
	public function test_plugin_include() {

		$plugin_root_dir = dirname( __DIR__, 2 ) . '/src';

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
			)
		);

		\WP_Mock::userFunction(
			'register_activation_hook'
		);

		\WP_Mock::userFunction(
			'register_deactivation_hook'
		);

		require_once $plugin_root_dir . '/bh-wp-plugin-update-git-diffs.php';

		$this->assertArrayHasKey( 'bh_wp_plugin_update_git_diffs', $GLOBALS );

		$this->assertInstanceOf( BH_WP_Plugin_Update_Git_Diffs::class, $GLOBALS['bh_wp_plugin_update_git_diffs'] );

	}


	/**
	 * Verifies the plugin does not output anything to screen.
	 */
	public function test_plugin_include_no_output() {

		$plugin_root_dir = dirname( __DIR__, 2 ) . '/src';

		\WP_Mock::userFunction(
			'plugin_dir_path',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'string' ) ),
				'return' => $plugin_root_dir . '/',
			)
		);

		\WP_Mock::userFunction(
			'register_activation_hook'
		);

		\WP_Mock::userFunction(
			'register_deactivation_hook'
		);

		ob_start();

		require_once $plugin_root_dir . '/bh-wp-plugin-update-git-diffs.php';

		$printed_output = ob_get_contents();

		ob_end_clean();

		$this->assertEmpty( $printed_output );

	}

}
