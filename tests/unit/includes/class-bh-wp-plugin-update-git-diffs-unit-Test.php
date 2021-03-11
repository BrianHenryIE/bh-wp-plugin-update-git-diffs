<?php
/**
 * @package BH_WP_Plugin_Update_Git_Diffs_Unit_Name
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Admin\Admin;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\API_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\API\Settings_Interface;
use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\NullLogger;
use WP_Mock\Matcher\AnyInstance;

/**
 * Class BH_WP_Plugin_Update_Git_Diffs_Unit_Test
 */
class BH_WP_Plugin_Update_Git_Diffs_Unit_Test extends \Codeception\Test\Unit {

	protected function _before() {
		\WP_Mock::setUp();
	}

	// This is required for `'times' => 1` to be verified.
	protected function _tearDown() {
		parent::_tearDown();
		\WP_Mock::tearDown();
	}

	/**
	 * @covers BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs::set_locale
	 */
	public function test_set_locale_hooked() {

		\WP_Mock::expectActionAdded(
			'plugins_loaded',
			array( new AnyInstance( I18n::class ), 'load_plugin_textdomain' )
		);

        $api = $this->makeEmpty(API_Interface::class );
        $settings = $this->makeEmpty(Settings_Interface::class );
        $logger = new NullLogger();

        new BH_WP_Plugin_Update_Git_Diffs( $api, $settings, $logger );
	}

    /**
     * @covers BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs::define_admin_hooks
     */
    public function test_admin_hooks() {

        \WP_Mock::expectActionAdded(
            'admin_enqueue_scripts',
            array( new AnyInstance( Admin::class ), 'enqueue_styles' )
        );

        $api = $this->makeEmpty(API_Interface::class );
        $settings = $this->makeEmpty(Settings_Interface::class );
        $logger = new NullLogger();

        new BH_WP_Plugin_Update_Git_Diffs( $api, $settings, $logger );
    }


    /**
     * @covers BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes\BH_WP_Plugin_Update_Git_Diffs::define_post_hooks
     */
    public function test_post_hooks() {

        \WP_Mock::expectActionAdded(
            'init',
            array( new AnyInstance( Post::class ), 'register_git_diff_post_type' )
        );

        \WP_Mock::expectActionAdded(
            'post_type_link',
            array( new AnyInstance( Post::class ), 'get_permalink'), 10, 4
        );

        $api = $this->makeEmpty(API_Interface::class );
        $settings = $this->makeEmpty(Settings_Interface::class );
        $logger = new NullLogger();

        new BH_WP_Plugin_Update_Git_Diffs( $api, $settings, $logger );
    }

}
