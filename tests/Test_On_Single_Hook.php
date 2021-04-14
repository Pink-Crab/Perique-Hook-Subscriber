<?php

declare(strict_types=1);

/**
 *
 * @since 1.0.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Hook_Subscriber\Tests;

use PinkCrab\Loader\Loader;
use PHPUnit\Framework\TestCase;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Hook_Subscriber\Hook_Subscriber;
use PinkCrab\Hook_Subscriber\Tests\Stubs\On_Single_Hook;

class Test_On_Single_Hook extends TestCase {

	/**
	 * Creats a new loader, and adds the passed subsctriber
	 * Before returning the loader.
	 *
	 * @param Hook_Subscriber $subscriber
	 * @return Loader
	 */
	protected function initialise_loader( Hook_Subscriber $subscriber ): Loader {
		$loader = new Loader();
		$subscriber->register( $loader );
		return $loader;
	}

	/**
	 * Test none deferred can accept 20 args if neeed.
	 *
	 * @return void
	 */
	public function test_subscribes_to_custom_action(): void {

		$subscriber = new On_Single_Hook();

		// add to loader and get loader.
		$loader = $this->initialise_loader( $subscriber );
		$loader->register_hooks();

		$subscription = new On_Single_Hook();

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
		$global = Reflection::get_private_property( $loader, 'hooks' );
		$hook   = $global->pop();

		// Check it has our set handle.
		$this->assertEquals( 'pc_on_single_hook', $hook->get_handle() );
	}
}
