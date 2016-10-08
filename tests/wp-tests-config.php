<?php

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define( 'DB_NAME', 'wordpress_tests' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );
$table_prefix = 'wptests_';   // Only numbers, letters, and underscores please!

//if ( file_exists( __DIR__ . '/custom-tests-config.php' ) ) {
//	require_once 'custom-tests-config.php';
//}

$abspath = getenv( 'WP_DIR' );
/* Path to the WordPress codebase you'd like to test. Add a forward slash in the end. */
if ( false === $abspath ) {
	if ( file_exists( __DIR__ . '/../../../../wp-settings.php' ) ) {
		$abspath = __DIR__ . '/../../../../';
	} elseif ( file_exists( __DIR__ . '/../../../../wp/wp-settings.php' ) ) {
		$abspath = __DIR__ . '/../../../../wp/';
	} else {
		die( 'WordPress not found' );
	}
}

defined( 'ABSPATH' ) or define( 'ABSPATH', $abspath );
defined( 'WP_TESTS_DOMAIN' ) or define( 'WP_TESTS_DOMAIN', 'example.org' );
defined( 'WP_TESTS_EMAIL' ) or define( 'WP_TESTS_EMAIL', 'admin@example.org' );
defined( 'WP_TESTS_TITLE' ) or define( 'WP_TESTS_TITLE', 'Test Blog' );

defined( 'WP_PHP_BINARY' ) or define( 'WP_PHP_BINARY', 'php' );
defined( 'WPLANG' ) or define( 'WPLANG', '' );
defined( 'WP_CONTENT_DIR' ) or define( 'WP_CONTENT_DIR', ABSPATH . '/wp-content' );
defined( 'WP_CONTENT_URL' ) or define( 'WP_CONTENT_URL', 'http://' . WP_TESTS_DOMAIN . '/wp-content' );
