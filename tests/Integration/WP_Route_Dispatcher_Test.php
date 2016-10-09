<?php
namespace GooseStudio\WpRoutes\Tests\Integration;

use ArtOfWP\WP\Testing\WP_UnitTestCase;
use GooseStudio\WpRoutes\WP_Route_Dispatcher;
use GooseStudio\WpRoutes\WP_Routes;
use WP_REST_Request;

/**
 * Class WP_Route_Dispatcher_Test
 * @package GooseStudio\WpRoutes\Tests\Integration
 */
class WP_Route_Dispatcher_Test extends WP_UnitTestCase {
	/**
	 * @var Spy_REST_Server
	 */
	private $server;
	private $dispatcher;

	public function setUp() {
		parent::setUp();
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		require_once ABSPATH . WPINC . '/rest-api.php';
		$this->dispatcher = WP_Route_Dispatcher::register();
		// Override the normal server with our spying server.
		$this->server = $GLOBALS['wp_rest_server'] = new Spy_REST_Server();
	}

	public function test_verify_filter_run() {
		$this->assertEquals( 0, has_action( 'rest_dispatch_request', array(
			$this->dispatcher,
			'rest_dispatch_request',
		) ) );
	}

	public function test_function_one_param_no_rest_request_type() {
		WP_Routes::get( 'test-ns/test', function ( \WP_REST_Request $request ) {
			return 'requested';
		} );
		$request  = new WP_REST_Request( 'GET', '/test-ns/test' );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 'requested', $response->get_data() );
	}

	public function test_function_one_param_id() {
		WP_Routes::get( 'test-ns/test/:id', function ( $id ) {
			return $id;
		}, array( 'id' => '\d+' ) );
		$request  = new WP_REST_Request( 'GET', '/test-ns/test/1' );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( '1', $response->get_data() );
	}

	public function test_function_many_params() {
		WP_Routes::get( 'test-ns/test/:id/bucket/:bucket_id', function ( $id, $bucket_id ) {
			return $id . ' ' . $bucket_id;
		}, array( 'id' => '\d+', 'bucket_id' => '\d+' ) );
		$request  = new WP_REST_Request( 'GET', '/test-ns/test/1/bucket/2' );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( '1 2', $response->get_data() );
	}

	public function test_function_many_params_and_request() {
		WP_Routes::get( 'test-ns/test/:id/bucket/:bucket_id', function ( $id, $bucket_id, WP_REST_Request $request ) {
			return $id . ' ' . $bucket_id . ' ' . $request['id'];
		}, array( 'id' => '\d+', 'bucket_id' => '\d+' ) );
		$request  = new WP_REST_Request( 'GET', '/test-ns/test/1/bucket/2' );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( '1 2 1', $response->get_data() );
	}

	public function tearDown() {
		parent::tearDown();
		// Remove our temporary spy server
		$GLOBALS['wp_rest_server'] = null;
		unset( $_REQUEST['_wpnonce'] );
		WP_Route_Dispatcher::reset();
	}
}
