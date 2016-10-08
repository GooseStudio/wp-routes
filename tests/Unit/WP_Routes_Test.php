<?php
namespace GooseStudio\WpRoutes\Tests\Unit;

use InvalidArgumentException;
use GooseStudio\WpRoutes\WP_Routes;
use phpmock\mockery\PHPMockery as f;

class WP_RoutesTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Check that a GET route is correctly called.
	 *
	 */
	public function test_route_get() {
		f::mock( get_ns( WP_Routes::class ), 'register_rest_route' )->withArgs( array(
				'test-ns',
				'test',
				array(
					'methods'  => array( 'GET' ),
					'callback' => '__return_null',
				),
			)
		)->once();
		WP_Routes::get( 'test-ns/test', '__return_null' );
	}

	/**
	 * Check that a POST route is correctly called.
	 *
	 */
	public function test_route_post() {
		f::mock( get_ns( WP_Routes::class ), 'register_rest_route' )->withArgs( array(
				'test-ns',
				'test',
				array(
					'methods'  => array( 'POST' ),
					'callback' => '__return_null',
				),
			)
		)->once();
		WP_Routes::create( 'test-ns/test', '__return_null' );
	}

	/**
	 * Check that a PUT route is correctly called.
	 *
	 */
	public function test_route_put() {
		f::mock( get_ns( WP_Routes::class ), 'register_rest_route' )->withArgs( array(
				'test-ns',
				'test',
				array(
					'methods'  => array( 'PUT' ),
					'callback' => '__return_null',
				),
			)
		)->once();
		WP_Routes::update( 'test-ns/test', '__return_null' );
	}

	/**
	 * Check that a single route is canonicalized.
	 *
	 * Ensures that single and multiple routes are handled correctly.
	 */
	public function test_route_canonicalized_groups() {
		f::mock( get_ns( WP_Routes::class ), 'register_rest_route' )->withArgs( array(
			'test-ns',
			'test',
			array(
				'methods'  => array( 'POST' ),
				'callback' => '__return_null',
			),
		) )->once();
		WP_Routes::group( 'test-ns',
			function () {
				WP_Routes::create( 'test', '__return_null' );
			}
		);
	}

	/**
	 * Check that a single route is canonicalized.
	 *
	 * Ensures that single and multiple routes are handled correctly.
	 */
	public function test_route_canonicalized_multiple() {
		f::mock( get_ns( WP_Routes::class ), 'register_rest_route' )->withAnyArgs()->twice();
		WP_Routes::group( 'test-ns',
			function () {
				WP_Routes::get( 'test', '__return_null' );
				WP_Routes::create( 'test', '__return_null' );
			}
		);
	}

	/**
	 * Check that a GET route is correctly called.
	 * @expectedException InvalidArgumentException
	 */
	public function test_route_get_empty_namespace() {
		WP_Routes::get( 'test', '__return_null' );
	}
}
