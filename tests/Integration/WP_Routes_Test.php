<?php
namespace GooseStudio\WpRoutes\Tests\Integration;

use ArtOfWP\WP\Testing\WP_UnitTestCase;
use GooseStudio\WpRoutes\WP_Routes;
use InvalidArgumentException;
use WP_REST_Request;

/**
 * Class WP_Routes_Test
 * @package GooseStudio\WpRoutes\Tests\Integration
 */
class WP_Routes_Test extends WP_UnitTestCase {
	/**
	 * @var Spy_REST_Server
	 */
	private $server;

	public function setUp() {
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		require_once ABSPATH . WPINC . '/rest-api.php';
		// Override the normal server with our spying server.
		$this->server = $GLOBALS['wp_rest_server'] = new Spy_REST_Server();
		parent::setUp();
	}

	public function tearDown() {
		// Remove our temporary spy server
		$GLOBALS['wp_rest_server'] = null;
		unset( $_REQUEST['_wpnonce'] );

		parent::tearDown();
	}

	/**
	 * Check that a single route is canonicalized.
	 *
	 * Ensures that single and multiple routes are handled correctly.
	 */
	public function test_route_canonicalized() {
		WP_Routes::get( 'test-ns/test', '__return_null' );

		// Check the route was registered correctly.
		$endpoints = $GLOBALS['wp_rest_server']->get_raw_endpoint_data();
		$this->assertArrayHasKey( '/test-ns/test', $endpoints );

		// Check the route was wrapped in an array.
		$endpoint = $endpoints['/test-ns/test'];
		$this->assertArrayNotHasKey( 'callback', $endpoint );
		$this->assertArrayHasKey( 'namespace', $endpoint );
		$this->assertEquals( 'test-ns', $endpoint['namespace'] );

		// Grab the filtered data.
		$filtered_endpoints = $GLOBALS['wp_rest_server']->get_routes();
		$this->assertArrayHasKey( '/test-ns/test', $filtered_endpoints );
		$endpoint = $filtered_endpoints['/test-ns/test'];
		$this->assertCount( 1, $endpoint );
		$this->assertArrayHasKey( 'callback', $endpoint[0] );
		$this->assertArrayHasKey( 'methods', $endpoint[0] );
		$this->assertArrayHasKey( 'args', $endpoint[0] );
	}

	/**
	 * Check that a single route is canonicalized.
	 *
	 * Ensures that single and multiple routes are handled correctly.
	 */
	public function test_route_canonicalized_multiple() {
		WP_Routes::group( 'test-ns',
			function () {
				WP_Routes::get( 'test-ns/test', '__return_null' );
				WP_Routes::create( 'test', '__return_null' );
			}
		);

		// Check the route was registered correctly.
		$endpoints = $GLOBALS['wp_rest_server']->get_raw_endpoint_data();
		$this->assertArrayHasKey( '/test-ns/test', $endpoints );

		// Check the route was wrapped in an array.
		$endpoint = $endpoints['/test-ns/test'];
		$this->assertArrayNotHasKey( 'callback', $endpoint );
		$this->assertArrayHasKey( 'namespace', $endpoint );
		$this->assertEquals( 'test-ns', $endpoint['namespace'] );

		$filtered_endpoints = $GLOBALS['wp_rest_server']->get_routes();
		$endpoint           = $filtered_endpoints['/test-ns/test'];
		$this->assertCount( 2, $endpoint );

		// Check for both methods.
		foreach ( array( 0, 1 ) as $key ) {
			$this->assertArrayHasKey( 'callback', $endpoint[ $key ] );
			$this->assertArrayHasKey( 'methods', $endpoint[ $key ] );
			$this->assertArrayHasKey( 'args', $endpoint[ $key ] );
		}
	}

	/**
	 * Check that routes are merged by default.
	 */
	public function test_route_merge() {
		WP_Routes::get( 'test-ns/test', '__return_null' );
		WP_Routes::create( 'test-ns/test', '__return_null' );

		// Check both routes exist.
		$endpoints = $GLOBALS['wp_rest_server']->get_routes();
		$endpoint  = $endpoints['/test-ns/test'];
		$this->assertCount( 2, $endpoint );
	}

	/**
	 * Check that we can override routes.
	 */
	//TODO: Not implemented
	/*	public function test_route_override() {
		register_rest_route( 'test-ns', '/test', array(
			'methods'      => array( 'GET' ),
			'callback'     => '__return_null',
			'should_exist' => false,
		) );
		register_rest_route( 'test-ns', '/test', array(
			'methods'      => array( 'POST' ),
			'callback'     => '__return_null',
			'should_exist' => true,
		), true );

		// Check we only have one route.
		$endpoints = $GLOBALS['wp_rest_server']->get_routes();
		$endpoint = $endpoints['/test-ns/test'];
		$this->assertCount( 1, $endpoint );

		// Check it's the right one.
		$this->assertArrayHasKey( 'should_exist', $endpoint[0] );
		$this->assertTrue( $endpoint[0]['should_exist'] );
	}*/

	/**
	 * Test that we reject routes without namespaces
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function test_route_reject_empty_namespace() {
		WP_Routes::create( '/test-empty-namespace', '__return_null' );
	}

	/**
	 * Test that we reject empty routes
	 *
	 * @expectedException InvalidArgumentException
	 */
	public function test_route_reject_empty_route() {
		WP_Routes::create( '/test-empty-route', '__return_null' );
	}

	public function test_route_method() {
		WP_Routes::get( 'test-ns/test', '__return_null' );

		$routes = $GLOBALS['wp_rest_server']->get_routes();

		$this->assertEquals( $routes['/test-ns/test'][0]['methods'], array( 'GET' => true ) );
	}

	/**
	 * The 'methods' arg should accept a single value as well as array.
	 */
	public function test_route_method_string() {
		WP_Routes::get( 'test-ns/test', '__return_null' );

		$routes = $GLOBALS['wp_rest_server']->get_routes();

		$this->assertEquals( $routes['/test-ns/test'][0]['methods'], array( 'GET' => true ) );
	}

	/**
	 * The 'methods' arg should accept a single value as well as array.
	 */
	public function test_route_get_with_param() {
		WP_Routes::get( 'test-ns/test/:id', '__return_null', array( 'id' => '\d+' ) );

		$routes = $GLOBALS['wp_rest_server']->get_routes();
		$this->assertArrayHasKey( '/test-ns/test/(?P<id>\d+)', $routes );
	}

	/**
	 * The 'methods' arg should accept a single value as well as array.
	 */
	public function test_route_get_with_params() {
		WP_Routes::get( 'test-ns/test/:id/bucket/:bucket_id', '__return_null', array(
			'id'        => '\d+',
			'bucket_id' => '\d+',
		) );

		$routes = $GLOBALS['wp_rest_server']->get_routes();
		$this->assertArrayHasKey( '/test-ns/test/(?P<id>\d+)/bucket/(?P<bucket_id>\d+)', $routes );
	}

	/**
	 * The 'methods' arg should accept a single value as well as array.
	 */
	public function test_route_create() {
		WP_Routes::create( 'test-ns/test', function() { return 'created'; } );
		$request  = new WP_REST_Request( 'POST', '/test-ns/test' );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 'created', $response->get_data() );
	}

	/**
	 * The 'methods' arg should accept a single value as well as array.
	 */
	//TODO: Not implemented
	/*public function test_route_method_array() {
		register_rest_route( 'test-ns', '/test', array(
			'methods'  => array( 'GET', 'POST' ),
			'callback' => '__return_null',
		) );

		$routes = $GLOBALS['wp_rest_server']->get_routes();

		$this->assertEquals( $routes['/test-ns/test'][0]['methods'], array( 'GET' => true, 'POST' => true ) );
	}*/

	/**
	 * The 'methods' arg should a comma seperated string.
	 */
	//TODO: Not implemented
	/*public function test_route_method_comma_seperated() {
		register_rest_route( 'test-ns', '/test', array(
			'methods'  => 'GET,POST',
			'callback' => '__return_null',
		) );

		$routes = $GLOBALS['wp_rest_server']->get_routes();

		$this->assertEquals( $routes['/test-ns/test'][0]['methods'], array( 'GET' => true, 'POST' => true ) );
	}*/

	public function test_options_request() {
		WP_Routes::get( 'test-ns/test', '__return_null' );
		$request  = new WP_REST_Request( 'OPTIONS', '/test-ns/test' );
		$response = rest_handle_options_request( null, $GLOBALS['wp_rest_server'], $request );
		$response = rest_send_allow_header( $response, $GLOBALS['wp_rest_server'], $request );
		$headers  = $response->get_headers();
		$this->assertArrayHasKey( 'Allow', $headers );

		$this->assertEquals( 'GET', $headers['Allow'] );
	}

	/**
	 * Ensure that the OPTIONS handler doesn't kick in for non-OPTIONS requests.
	 */
	public function test_options_request_not_options() {
		WP_Routes::get( 'test-ns/test', '__return_null' );

		$request  = new WP_REST_Request( 'GET', '/test-ns/test' );
		$response = rest_handle_options_request( null, $GLOBALS['wp_rest_server'], $request );

		$this->assertNull( $response );
	}

	public function test_serve_request_url_param() {
		WP_Routes::get( 'test-ns/test/:id', '__return_null', array( 'id' => '\d+' ) );

		$this->server->serve_request( '/test-ns/test/1' );
		$url_params = $this->server->last_request->get_url_params();
		$this->assertEquals( '1', $url_params['id'] );
	}

	public function test_serve_request_url_params() {
		WP_Routes::get( 'test-ns/test/:id/bucket/:bucket_id', '__return_null', array(
			'id'        => '\d+',
			'bucket_id' => '\d+',
		) );

		$this->server->serve_request( '/test-ns/test/1/bucket/2' );
		$url_params = $this->server->last_request->get_url_params();
		$this->assertEquals( '1', $url_params['id'] );
		$this->assertEquals( '2', $url_params['bucket_id'] );
	}

	public function jsonp_callback_provider() {
		return array(
			// Standard names
			array( 'Springfield', true ),
			array( 'shelby.ville', true ),
			array( 'cypress_creek', true ),
			array( 'KampKrusty1', true ),

			// Invalid names
			array( 'ogden-ville', false ),
			array( 'north haverbrook', false ),
			array( "Terror['Lake']", false ),
			array( 'Cape[Feare]', false ),
			array( '"NewHorrorfield"', false ),
			array( 'Scream\\ville', false ),
		);
	}

	/**
	 * @dataProvider jsonp_callback_provider
	 */
	public function test_jsonp_callback_check( $callback, $valid ) {
		$this->assertEquals( $valid, wp_check_jsonp_callback( $callback ) );
	}

}
