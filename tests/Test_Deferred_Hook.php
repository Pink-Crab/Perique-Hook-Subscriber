<?php

declare(strict_types=1);

/**
 *
 * @since 1.0.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Hook_Subscriber\Tests;

use PHPUnit\Framework\TestCase;
use PinkCrab\Hook_Subscriber\Tests\Stubs\Deferred_Hook;

class Test_Deferred_Hook extends TestCase {

	/**
	 * Test that a deferred hook fires first, and allows the main
	 * subscbriber to access the current global scope.
	 *
	 * Final hook call, should have the global value set during deferred
	 * hook call.
	 *
	 * @return void
	 */
	public function test_run(): void {

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
		do_action( 'pc_pre_deferred_hook');
		do_action( 'pc_on_deferred_hook', '42' );

		// Test our action was create on pc_pre_deferred_hook call.
		$this->assertEquals(
			'called on pc_pre_deferred_hook',
			( new Deferred_Hook() )::$log['42']
		);

	}
}
