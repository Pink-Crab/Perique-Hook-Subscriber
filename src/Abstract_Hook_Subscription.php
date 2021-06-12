<?php

declare(strict_types=1);
/**
 * Abstract Class for registering single hook subscriber.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package DIPT\Hook_Subscriber
 */

namespace PinkCrab\Hook_Subscriber;

use InvalidArgumentException;
use PinkCrab\Loader\Hook_Loader;
use PinkCrab\Perique\Application\App;
use PinkCrab\Hook_Subscriber\Hook_Subscriber;

abstract class Abstract_Hook_Subscription implements Hook_Subscriber {

	/**
	 * The hook to register the subscriber
	 *
	 * @var string|null
	 */
	protected $hook;

	/**
	 * Optional hook to use to register the primary hook.
	 *
	 * @var string|null
	 */
	protected $deferred_hook;

	/**
	 * Hook call priority
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Number of args passed.
	 * Default set to 10 to allow execute to use spread op.
	 *
	 * @var int
	 */
	protected $args = 20;

	/**
	 * Registers subscriber to the loader.
	 *
	 * @param Hook_Loader $loader
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function register( Hook_Loader $loader ): void {

		if ( ! is_string( $this->hook ) || empty( $this->hook ) ) {
			throw new InvalidArgumentException( \sprintf( 'Hook not defined in %s subscriber', static::class ) );
		}

		// If we have a differed event.
		if ( ! empty( $this->deferred_hook ) ) {
			$loader->action(
				$this->deferred_hook,
				function( ...$args ) { // phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface

					// Create a new isntance using DI and register using the defined hook.
					$instance = App::make( static::class );
					if ( is_callable( array( $instance, 'execute' ) ) ) { // Ensure valid callable
						\add_action( (string) $this->hook, array( $instance, 'execute' ), $this->priority, $this->args );
					}
				},
				$this->priority,
				$this->args
			);
		} else {
			$loader->action(
				$this->hook,
				array( $this, 'execute' ),
				$this->priority,
				$this->args
			);
		}
	}

	/**
	 * Callback back method.
	 *
	 * @param mixed ...$args
	 * @return void
	 */
	abstract public function execute( ...$args ): void;
}
