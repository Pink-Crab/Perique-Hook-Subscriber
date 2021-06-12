<?php

declare(strict_types=1);

/**
 *
 * @since 1.0.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Hook_Subscriber\Tests;

use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Hook_Subscriber\Hook_Subscriber;
use PinkCrab\Hook_Subscriber\Tests\Stubs\On_Single_Hook;

class Test_On_Single_Hook extends TestCase {

	/**
	 * Creates a new loader, and adds the passed subscriber
	 * Before returning the loader.
	 *
	 * @param Hook_Subscriber $subscriber
	 * @return Hook_Loader
	 */
	protected function initialise_loader( Hook_Subscriber $subscriber ): Hook_Loader {
		$loader = new Hook_Loader();
		$subscriber->register( $loader );
		return $loader;
	}

	/**
	 * Test none deferred can accept 10 args if needed.
	 *
	 * @return void
	 */
	public function test_subscribes_to_custom_action(): void {

		$subscription = new On_Single_Hook();

		// add to loader and get loader.
		$loader = $this->initialise_loader( $subscription );
		$loader->register_hooks();


		// Test args are passed and picked up by subscriber.
		do_action( 'pc_on_single_hook', 'crab', 42 );

		$this->assertCount( 2, $subscription::$log );
		$this->assertContains( 'crab', $subscription::$log );
		$this->assertContains( 42, $subscription::$log );

		// Clear Log
		$subscription::$log = array();

		// Test 20 args.
		do_action( 'pc_on_single_hook', ...array_keys( range( 0, 9 ) ) );

		$this->assertCount( 10, $subscription::$log );
		for ( $i = 0; $i < 10; $i++ ) {
			$this->assertContains( $i, $subscription::$log );
		}

		// Clear Log
		$subscription::$log = array();
	}

	/**
	 * Tests the correct hook is added to the loader.
	 *
	 * @return void
	 */
	public function test_hook_added_to_loader(): void {
		$subscriber = new On_Single_Hook();

		// add to loader and get loader.
		$loader = $this->initialise_loader( $subscriber );

		// Get the registered hook form the loader
		$global = Objects::get_property( $loader, 'hooks' );
		$hook   = $global->pop();

		// Check it has our set handle.
		$this->assertEquals( 'pc_on_single_hook', $hook->get_handle() );
	}
}
