<?php
/**
 * @package           BH_WP_Plugin_Update_Git_Diffs
 */

$GLOBALS['project_root_dir']   = $project_root_dir  = dirname( __FILE__, 2 );
$GLOBALS['plugin_root_dir']    = $plugin_root_dir   = $project_root_dir . '/src';
$GLOBALS['plugin_name']        = $plugin_name       = basename( $project_root_dir );
$GLOBALS['plugin_name_php']    = $plugin_name_php   = $plugin_name . '.php';
$GLOBALS['plugin_path_php']                         = $plugin_root_dir . '/' . $plugin_name_php;
$GLOBALS['plugin_basename']                         = $plugin_name . '/' . $plugin_name_php;
$GLOBALS['wordpress_root_dir']                      = $project_root_dir . '/wordpress';


