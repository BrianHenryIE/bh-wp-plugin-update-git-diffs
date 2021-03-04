<?php
/**
 *
 *
 * @package BH_WP_Plugin_Update_Git_Diffs
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BH_WP_Plugin_Update_Git_Diffs\Includes;

/**
 * Class Plugin_WP_Mock_Test
 *
 * @covers \BH_WP_Plugin_Update_Git_Diffs\Includes\I18n
 */
class I18n_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// This is required for `'times' => 1` to be verified.
	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * Verify load_plugin_textdomain is correctly called.
	 *
	 * @covers I18n::load_plugin_textdomain
	 */
	public function test_load_plugin_textdomain() {

		global $plugin_root_dir;

		\WP_Mock::userFunction(
			'load_plugin_textdomain',
			array(
				'args'   => array(
					'bh-wp-plugin-update-git-diffs',
					false,
					$plugin_root_dir . '/languages/',
				)
			)
		);
	}
}
