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
use PinkCrab\Hook_Subscriber\Tests\Stubs\Deferred_Hook;

class Test_Deferred_Hook extends TestCase {

	/**
	 * Create a new loader, and adds the passed subscriber
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
	 * Test that a deferred hook fires first, and allows the main
	 * subscriber to access the current global scope.
	 *
	 * Final hook call, should have the global value set during deferred
	 * hook call.
	 *
	 * @return void
	 */
	public function test_run(): void {
		// add to loader and get loader.
		$this->initialise_loader( new Deferred_Hook() )->register_hooks();

		// Populate mock global and call deferred hook.
		add_action(
			'pc_pre_deferred_hook',
			function( $e ) {
				// Set the global to be set on.
				global $deferred_global;
				$deferred_global = 'called on pc_pre_deferred_hook';
			},
			9,
			1
		);

		// Call the 2 actions.
		do_action( 'pc_pre_deferred_hook' );
		do_action( 'pc_on_deferred_hook', '42' );

		// Test our action was create on pc_pre_deferred_hook call.
		$this->assertEquals(
			'called on pc_pre_deferred_hook',
			( new Deferred_Hook() )::$log['42']
		);

	}

	/**
	 * Tests the correct hook is added to the loader.
	 *
	 * @return void
	 */
	public function test_hook_added_to_loader(): void {
		$subscriber = new Deferred_Hook();

		// add to loader and get loader.
		$loader = $this->initialise_loader( $subscriber );

		// Get the registered hook form the loader
		$global = Objects::get_property( $loader, 'hooks' );
		$hook   = $global->pop();

		// Check it has our set handle.
		$this->assertEquals( 'pc_pre_deferred_hook', $hook->get_handle() );
	}


}
