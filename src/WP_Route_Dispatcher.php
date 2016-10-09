<?php
namespace GooseStudio\WpRoutes;


use ReflectionFunction;

class WP_Route_Dispatcher {
	private static $registered = false;

	/**
	 * Register dispatcher
	 */
	public static function register() {
		if ( self::$registered ) {
			return;
		}
		self::$registered = true;
		$dispatcher       = new WP_Route_Dispatcher();
		add_filter( 'rest_dispatch_request', array( $dispatcher, 'rest_dispatch_request' ), 0, 4 );

		return $dispatcher;
	}

	/**
	 * Register dispatcher
	 */
	public static function reset() {
		self::$registered = false;
	}

	/**
	 * @param string $response
	 * @param \WP_REST_Request $request
	 * @param string $route
	 * @param array $handler
	 *
	 * @return mixed
	 */
	public function rest_dispatch_request( $response, $request, $route, $handler ) {
		if ( null !== $response ) {
			return $response;
		}
		$callback = $handler['callback'];
		if ( is_callable( $callback ) && ! is_array( $callback ) ) {
			$function = new ReflectionFunction( $callback );
			$params   = [];
			if ( $function->getNumberOfRequiredParameters() ) {
				foreach ( $function->getParameters() as $param ) {
					if ( isset( $request[ $param->getName() ] ) ) {
						$params[] = $request[ $param->getName() ];
					} elseif ( $param->getClass() === null || 'WP_REST_Request' === $param->getClass()->getName() ) {
						$params[] = $request;
					}
				}
				if ( ! empty( $params ) ) {
					return call_user_func_array( $callback, $params );
				}
			}
		} elseif ( is_callable( $callback ) && is_array( $callback ) ) {
			$method = new \ReflectionMethod( $callback[0], $callback[1] );
			$params = [];
			if ( $method->getNumberOfRequiredParameters() ) {
				foreach ( $method->getParameters() as $param ) {
					if ( isset( $request[ $param->getName() ] ) ) {
						$params[] = $request[ $param->getName() ];
					} elseif ( $param->getClass() === null || 'WP_REST_Request' === $param->getClass()->getName() ) {
						$params[] = $request;
					}
				}
				if ( ! empty( $params ) ) {
					return call_user_func_array( $callback, $params );
				}
			}
		}

		return $response;
	}
}
