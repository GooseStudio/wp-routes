<?php
use ArtOfWP\WP\Testing\WP_Bootstrap;

( new WP_Bootstrap( '/var/www/social-foundation.dev/public/wp', __DIR__ . '/wp-tests-config.php' ) )->run();
