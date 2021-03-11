<?php
/**
 * Tests for the root plugin file.
 *
 * @package BH_WP_Plugin_Update_Git_Diffs
 * @author  BrianHenryIE <BrianHenryIE@gmail.com>
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\API;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\NullLogger;


class API_WPUnit_Test extends \Codeception\TestCase\WPTestCase {


	public function test_1() {

	    $logger = new NullLogger();

	    $sut = new API( $logger );

        $path_1 = '/Users/brianhenry/Sites/bh-wp-plugin-update-git-diffs/tests/_data/private/wc-easypost-shipping-pro-1.3.2';
        $path_2 = '/Users/brianhenry/Sites/bh-wp-plugin-update-git-diffs/tests/_data/private/wc-easypost-shipping-pro-1.4.4';

        $diff = $sut->diff_dirs( $path_1, $path_2 );


    }

    public function noest_2() {

        $logger = new NullLogger();

        $sut = new API( $logger );

        $update = (object) array(
            'id' => 'w.org/plugins/bh-wp-autologin-urls',
            'slug' => 'bh-wp-autologin-urls',
            'plugin' => 'bh-wp-autologin-urls/bh-wp-autologin-urls.php',
            'new_version' => '1.1.2',
            'url' => 'https://wordpress.org/plugins/bh-wp-autologin-urls/',
            'package' => 'https://downloads.wordpress.org/plugin/bh-wp-autologin-urls.zip',
            'icons' =>
                array (
                    'default' => 'https://s.w.org/plugins/geopattern-icon/bh-wp-autologin-urls.svg',
                ),
            'banners' =>
                array (
                ),
            'banners_rtl' =>
                array (
                ),
            'tested' => '5.3.6',
            'requires_php' => '5.7',
            'compatibility' =>
                (object) array(
                ),
        );

        $sut->process_available_update( $update );
    }
}
