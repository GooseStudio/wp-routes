<?php
use ArtOfWP\WP\Testing\WP_Bootstrap;

$abspath = getenv( 'WP_DIR' );
if ( false === $abspath ) {
	if ( file_exists( __DIR__ . '/../../../../wp-settings.php' ) ) {
		$abspath = __DIR__ . '/../../../../';
	} elseif ( file_exists( __DIR__ . '/../../../../wp/wp-settings.php' ) ) {
		$abspath = __DIR__ . '/../../../../wp/';
	} else {
		die( 'WordPress not found' );
	}
}
( new WP_Bootstrap( $abspath, __DIR__ . '/wp-tests-config.php' ) )->run();
