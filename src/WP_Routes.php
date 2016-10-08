<?php
namespace GooseStudio\WpRoutes;

/**
 * Class WP_Routes
 * @package GooseStudio
 */
class WP_Routes {
	private static $prefix;

	/**
	 * @param string $path
	 * @param callback $callback
	 */
	public static function get( $path, $callback ) {
		$namespace = self::get_namespace( $path );
		$route     = self::get_route( $path );
		self::register_rest_route( 'GET', $namespace, $route, $callback );
	}

	/**
	 * @param string $path
	 * @param callback $callback
	 */
	public static function group( $path, $callback ) {
		self::$prefix = $path;
		$callback();
		self::$prefix = '';
	}

	/**
	 * @param string $path
	 * @param callback $callback
	 */
	public static function create( $path, $callback ) {
		$namespace = self::get_namespace( $path );
		$route     = self::get_route( $path );
		self::register_rest_route( 'POST', $namespace, $route, $callback );
	}

	/**
	 * @param string $path
	 * @param callback $callback
	 */
	public static function update( $path, $callback ) {
		$namespace = self::get_namespace( $path );
		$route     = self::get_route( $path );
		self::register_rest_route( 'PUT', $namespace, $route, $callback );

	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	public static function get_namespace( $path ) {
		if ( empty( self::$prefix ) ) {
			$namespace = substr( $path, 0, strpos( $path, '/' ) );
			if ( empty( $namespace ) ) {
				throw new \InvalidArgumentException( 'Route does not have a namespace' );
			}

			return $namespace;
		} else {
			$namespace = self::$prefix;

			return $namespace;
		}
	}

	/**
	 * @param string $method
	 * @param string $namespace
	 * @param string $route
	 * @param callback $callback
	 */
	private static function register_rest_route( $method, $namespace, $route, $callback ) {
		register_rest_route( $namespace, $route, array(
				'methods'  => array( $method ),
				'callback' => $callback,
			)
		);
	}

	/**
	 * @param $path
	 *
	 * @return string
	 */
	private static function get_route( $path ) {
		$route = substr( $path, ( $pos = strpos( $path, '/' ) ) ? $pos + 1 : 0 );

		return $route;
	}
}
