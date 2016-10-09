# wp-routes
[![Build Status](https://travis-ci.org/GooseStudio/wp-routes.svg?branch=master)](https://travis-ci.org/GooseStudio/wp-routes) 
[![codecov](https://codecov.io/gh/GooseStudio/wp-routes/branch/master/graph/badge.svg)](https://codecov.io/gh/GooseStudio/wp-routes)



**Experimental**

A wrapper framework for the WordPress REST API


## Usage

*Basic get route:*

```WP_Routes::get('namespace/test', 'prefix_my_response_function');```

Basic get route with param: 

```WP_Routes::get('namespace/test/:id', 'prefix_my_response_function', array( 'id' => '\d+' ) );```

Basic grouping of routes with param: 

```
WP_Routes::group( 'namespace/v1/test/', function() {
    //Since WP REST API can't handle class names you need to instantiate objects before hand.
    $controller = new MyController();
    WP_Routes::create( '', [$controller, 'create']); 
    WP_Routes::get( ':id', [$controller, 'get'], array( 'id' => '\d+' ) ); 
    WP_Routes::put( ':id', [$controller, 'update'], array( 'id' => '\d+' ) ); 
    WP_Routes::delete( ':id', [$controller, 'delete_'], array( 'id' => '\d+' ) ); 
 }
);
```