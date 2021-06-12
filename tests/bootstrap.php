<?php

use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Hook_Subscriber\Tests\Stubs\Deferred_Hook;
use PinkCrab\Hook_Subscriber\Tests\Stubs\On_Single_Hook;

/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function() {
		$app = ( new App_Factory )->with_wp_dice( true );

		add_action(
			'init',
			function () use ( $app ) {

				// Mock global.
				global $deferred_global;
				$deferred_global = 'init';

				// Register our test subscribers.
				$app->registration_classes( array( On_Single_Hook::class, Deferred_Hook::class ) );
				$app->boot();
			},
			1
		);
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
