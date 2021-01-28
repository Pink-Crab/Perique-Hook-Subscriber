<?php

declare(strict_types=1);

/**
 *
 * @since 1.0.0
 * @author GLynn Quelch <glynn.quelch@gmail.com>
 */

namespace PinkCrab\Hook_Subscriber\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Hook_Subscriber\Hook_Subscriber;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Hook_Subscriber\Tests\Stubs\On_Single_Hook;

class Test_Failures extends TestCase {

	/**
	 * Creats a new loader, and adds the passed subsctriber
	 * Before returning the loader.
	 *
	 * @param Hook_Subscriber $subscriber
	 * @return Loader
	 */
	public function initialise_loader( Hook_Subscriber $subscriber ) {
		$loader = new Loader();
		$subscriber->register( $loader );
		return $loader;
	}

	/**
	 * Test that an exception is thrown if no hook defined.
	 *
	 * @return void
	 */
	public function test_throw_exception_with_no_hook(): void {

		$this->expectException( InvalidArgumentException::class );

		// Create sunscriber and clear the hook (using reflection).
		$subscriber = new On_Single_Hook();
		Reflection::set_private_property( $subscriber, 'hook', '' );

		// Run the loader and catch exception.
		$this->initialise_loader( $subscriber )->register_hooks();

	}


}
