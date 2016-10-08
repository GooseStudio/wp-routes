<?php
namespace GooseStudio\WpRoutes\Tests\Integration;

use ArtOfWP\WP\Testing\WP_UnitTestCase;

/**
 * Class WP_Test_REST_TestCase
 * @package GooseStudio\Tests\Integration
 */
abstract class WP_Test_REST_TestCase extends WP_UnitTestCase {
	/**
	 * @param string $code
	 * @param \WP_REST_Response $response
	 * @param null $status
	 */
	protected function assertErrorResponse( $code, $response, $status = null ) {

		if ( is_a( $response, 'WP_REST_Response' ) ) {
			$response = $response->as_error();
		}

		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertEquals( $code, $response->get_error_code() );

		if ( null !== $status ) {
			$data = $response->get_error_data();
			$this->assertArrayHasKey( 'status', $data );
			$this->assertEquals( $status, $data['status'] );
		}
	}
}
