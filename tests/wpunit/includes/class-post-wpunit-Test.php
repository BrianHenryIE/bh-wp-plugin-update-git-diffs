<?php

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Psr\Log\NullLogger;

/**
 */
class Post_WPUnit_Test extends \Codeception\TestCase\WPTestCase {

    public function test_post_type_registered() {

        $sut = new Post( new NullLogger() );

        $sut->register_git_diff_post_type();

        $wp_post_types = get_post_types();

        $this->assertContains('plugin_update_diff', $wp_post_types);

    }
}
