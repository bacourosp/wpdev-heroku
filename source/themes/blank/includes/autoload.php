<?php
/**
 * WPBP autoloader
 *
 * @package    WP Theme Dev
 * @subpackage Blank Theme
 * @since      0.2.0
 */

namespace Blank;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$composer_autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( file_exists( $composer_autoloader ) ) {
	require_once $composer_autoloader;
}

unset( $composer_autoloader );
// phpcs:enable

spl_autoload_register( function ( $class_name ) {
	if ( strpos( $class_name, __NAMESPACE__ ) !== 0 ) {
		return;
	}

	$path = get_theme_file_path( strtolower( str_replace(
		[ '\\', __NAMESPACE__, '_' ],
		[ DIRECTORY_SEPARATOR, 'includes', '-' ],
		$class_name
	) ) . '.php' );

	if ( file_exists( $path ) ) {
		require_once $path;
	}
} );

$blank_files = new \RecursiveIteratorIterator(
	new \RecursiveDirectoryIterator( __DIR__ )
);

/**
 * @var \SplFileInfo $blank_file
 */
foreach ( $blank_files as $blank_file ) {
	if ( substr( $blank_file->getFilename(), -14 ) === '-functions.php' ) {
		require_once $blank_file->getPathname();
	}
}

unset( $blank_files, $blank_file );

if ( ! function_exists( 'dump' ) && env( 'WP_ENV' ) !== 'production' ) {
	/**
	 * Dump data.
	 *
	 * @return void
	 */
	function dump() {
		array_map( function ( $arg ) {
			var_dump( $arg ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
		}, func_get_args() );
	}
}