<?php
namespace GooseStudio\WpRoutes\Tests\Unit;

use GooseStudio\WpRoutes\WP_Route_Dispatcher;

/**
 * Class WP_Route_Dispatcher_Test
 * @package GooseStudio\WpRoutes\Tests\Unit
 */
class WP_Route_Dispatcher_Test extends \PHPUnit_Framework_TestCase {
	public function test_function_one_param_no_rest_request_type() {
		$dispatcher = new WP_Route_Dispatcher();
		$test_class = $this;
		$mock       = new \WP_REST_Request();
		$func       = function ( $request ) use ( $test_class, $mock ) {
			$test_class->assertEquals( $mock, $request );
		};
		$dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => $func ] );
	}

	public function test_function_one_param_id() {
		$dispatcher = new WP_Route_Dispatcher();
		$test_class = $this;
		$mock       = new \WP_REST_Request();
		$mock['id'] = 1;
		$func       = function ( $id ) use ( $test_class ) {
			$test_class->assertEquals( 1, $id );
		};
		$dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => $func ] );
	}

	public function test_function_many_params() {
		$dispatcher        = new WP_Route_Dispatcher();
		$test_class        = $this;
		$mock              = new \WP_REST_Request();
		$mock['id']        = 1;
		$mock['bucket_id'] = 2;
		$func              = function ( $id, $bucket_id ) use ( $test_class ) {
			$test_class->assertEquals( 1, $id );
			$test_class->assertEquals( 2, $bucket_id );
		};
		$dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => $func ] );
	}

	public function test_function_many_params_and_request() {
		$dispatcher        = new WP_Route_Dispatcher();
		$test_class        = $this;
		$mock              = new \WP_REST_Request();
		$mock['id']        = 1;
		$mock['bucket_id'] = 2;
		$func              = function ( $id, $bucket_id, \WP_REST_Request $request ) use ( $test_class, $mock ) {
			$test_class->assertEquals( 1, $id );
			$test_class->assertEquals( 2, $bucket_id );
			$test_class->assertEquals( $mock, $request );
		};
		$dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => $func ] );
	}

	public function test_object_one_param_no_rest_request_type() {
		$dispatcher = new WP_Route_Dispatcher();
		$mock       = new \WP_REST_Request();
		$controller = new Controller();
		$result = $dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => array( $controller, 'get' ) ] );
		$this->assertEquals( $mock, $result );
	}

	public function test_object_one_param_id() {
		$dispatcher = new WP_Route_Dispatcher();
		$mock       = new \WP_REST_Request();
		$mock['id'] = 1;
		$controller = new Controller();
		$result = $dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => array( $controller, 'get_one' ) ] );
		$this->assertEquals( 1, $result );
	}

	public function test_object_many_params() {
		$dispatcher        = new WP_Route_Dispatcher();
		$mock              = new \WP_REST_Request();
		$mock['id']        = 1;
		$mock['bucket_id'] = 2;
		$controller = new Controller();
		$result = $dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => array( $controller, 'get_two' ) ] );
		$this->assertEquals( '1 2', $result );
	}

	public function test_object_many_params_and_request() {
		$dispatcher        = new WP_Route_Dispatcher();
		$mock              = new \WP_REST_Request();
		$mock['id']        = 1;
		$mock['bucket_id'] = 2;
		$controller = new Controller();
		$result = $dispatcher->rest_dispatch_request( null, $mock, '', [ 'callback' => array( $controller, 'get_two_and_request' ) ] );
		$this->assertEquals( '1 2 requested', $result );
	}
}

class Controller {
	public function get( \WP_REST_Request $request ) {
		return $request;
	}
	public function get_one( $id ) {
		return $id;
	}
	public function get_two( $id, $bucket_id ) {
		return $id . ' ' . $bucket_id;
	}
	public function get_two_and_request( $id, $bucket_id, \WP_REST_Request $request ) {
		return $id . ' ' . $bucket_id . ' requested';
	}

}
