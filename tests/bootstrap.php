<?php
require_once __DIR__ . '/../vendor/autoload.php';
/**
 * @param $class
 *
 * @return string
 */
function get_ns( $class ) {
	return substr( $class, 0, strrpos( $class, '\\' ) );
}
