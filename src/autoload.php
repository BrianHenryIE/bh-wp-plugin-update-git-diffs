<?php
/**
 * Loads all required classes
 *
 * Uses classmap, PSR4 & wp-namespace-autoloader.
 *
 * @link              https://BrianHenryIE.com
 * @since             1.0.0
 * @package           BH_WP_Plugin_Update_Git_Diffs
 *
 * @see https://github.com/pablo-sg-pacheco/wp-namespace-autoloader/
 */

namespace BrianHenryIE\WP_Plugin_Update_Git_Diffs;

use BrianHenryIE\WP_Plugin_Update_Git_Diffs\Mozart\Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader;

$class_maps = array(
	__DIR__ . '/autoload-classmap.php',
	__DIR__ . '/Mozart/autoload-classmap.php',
);
foreach ( $class_maps as $class_map_file ) {
	if ( file_exists( $class_map_file ) ) {

		$class_map = include $class_map_file;

		if ( is_array( $class_map ) ) {
			spl_autoload_register(
				function ( $classname ) use ( $class_map ) {

					if ( array_key_exists( $classname, $class_map ) && file_exists( $class_map[ $classname ] ) ) {
						require_once $class_map[ $classname ];
					}
				}
			);
		}
	}
}


$wpcs_autoloader = new WP_Namespace_Autoloader();
$wpcs_autoloader->init();
