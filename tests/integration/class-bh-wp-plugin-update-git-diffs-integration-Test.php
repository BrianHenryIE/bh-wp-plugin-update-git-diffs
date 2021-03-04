<?php
/**
 * Class Plugin_Test. Tests the root plugin setup.
 *
 * @package BH_WP_Plugin_Update_Git_Diffs
 * @author     BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BH_WP_Plugin_Update_Git_Diffs;

use BH_WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs;

/**
 * Verifies the plugin has been instantiated and added to PHP's $GLOBALS variable.
 */
class Plugin_Integration_Test extends \Codeception\TestCase\WPTestCase {

	/**
	 * Test the main plugin object is added to PHP's GLOBALS and that it is the correct class.
	 */
	public function test_plugin_instantiated() {

		$this->assertArrayHasKey( 'bh_wp_plugin_update_git_diffs', $GLOBALS );

		$this->assertInstanceOf( BH_WP_Plugin_Update_Git_Diffs::class, $GLOBALS['bh_wp_plugin_update_git_diffs'] );
	}

}
