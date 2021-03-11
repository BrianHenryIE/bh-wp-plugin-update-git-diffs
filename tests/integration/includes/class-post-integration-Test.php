<?php

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs\Includes;

/**
 *
 */
class Post_Integration_Test extends \Codeception\TestCase\WPTestCase {

    public function test_post_type_is_registered() {

        $this->go_to( 'wp-admin/');

        $registered_post_types = get_post_types();

        $this->assertContains( 'plugin_git_diff', $registered_post_types );

    }

}
