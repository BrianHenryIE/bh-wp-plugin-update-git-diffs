<?php
/**
 * Tests for Admin.
 *
 * @see Admin
 *
 * @package bh-wp-plugin-update-git-diffs
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin;


use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\Settings_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\NullLogger;

/**
 * Class Admin_Test
 *
 * @covers \BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin\Admin
 */
class Admin_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// This is required for `'times' => 1` to be verified.
	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * The plugin name. Unlikely to change.
	 *
	 * @var string Plugin name.
	 */
	private $plugin_name = 'bh-wp-plugin-update-git-diffs';

	/**
	 * The plugin version, matching the version these tests were written against.
	 *
	 * @var string Plugin version.
	 */
	private $version = '1.0.0';

	/**
	 * Verifies enqueue_styles() calls wp_enqueue_style() with appropriate parameters.
	 * Verifies the .css file exists.
	 *
	 * @covers BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin\Admin::enqueue_styles
	 * @see wp_enqueue_style()
	 */
	public function test_enqueue_styles() {

		global $plugin_root_dir;

		// Return any old url.
		\WP_Mock::userFunction(
			'plugin_dir_url',
			array(
				'return' => $plugin_root_dir . '/admin/',
			)
		);

		$css_file = $plugin_root_dir . '/admin/css/bh-wp-plugin-update-git-diffs-admin.css';

		\WP_Mock::userFunction(
			'wp_enqueue_style',
			array(
				'times' => 1,
				'args'  => array( $this->plugin_name, $css_file, array(), $this->version, 'all' ),
			)
		);

        $settings = $this->makeEmpty( Settings_Interface::class,
            array(
                'get_plugin_version' => '1.0.0'
            ));		$logger = new NullLogger();

		$admin = new Admin( $settings, $logger );

		$admin->enqueue_styles();

		$this->assertFileExists( $css_file );
	}


}
