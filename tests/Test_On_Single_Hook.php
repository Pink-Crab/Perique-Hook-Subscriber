<?php

declare(strict_types=1);

/**
 *
 * @since 1.0.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Hook_Subscriber\Tests;

use PHPUnit\Framework\TestCase;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Hook_Subscriber\Tests\Stubs\On_Single_Hook;

class Test_On_Single_Hook extends TestCase {

	/**
	 * Test none deferred can accept 20 args if neeed.
	 *
	 * @return void
	 */
	public function test_subscribes_to_custom_action(): void {

		$subscription = new On_Single_Hook();

		// Test args are passed and picked up by subscriber.
		do_action( 'pc_on_single_hook', 'crab', 42 );

		$this->assertCount( 2, $subscription::$log );
		$this->assertContains( 'crab', $subscription::$log );
		$this->assertContains( 42, $subscription::$log );

		// Clear Log
		$subscription::$log = array();

		// Test 20 args.
		do_action( 'pc_on_single_hook', ...array_keys( range( 0, 19 ) ) );

		$this->assertCount( 20, $subscription::$log );
		for ( $i = 0; $i < 20; $i++ ) {
			$this->assertContains( $i, $subscription::$log );
		}

		// Clear Log
		$subscription::$log = array();

	}
}
