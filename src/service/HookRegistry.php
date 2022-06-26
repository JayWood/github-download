<?php
declare(strict_types=1);
namespace com\plugish\plugins\githubdownloads\service;

use com\plugish\plugins\githubdownloads\infrastructure\Runnable;
use Psr\Container\ContainerInterface;

class HookRegistry implements Runnable {
	private ContainerInterface $container;
	private array $runnable;

	/**
	 * HookRegistry constructor.
	 *
	 * @param ContainerInterface $container
	 * @param Runnable[] $runnable
	 */
	public function __construct( ContainerInterface $container, array $runnable ) {
		$this->container = $container;
		$this->runnable  = $runnable;
	}

	/**
	 * Runs every runnable registered.
	 *
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function run() {
		foreach ( $this->runnable as $class ) {
			$this->locate( $class )->run();
		}
	}

	/**
	 * @param string $class The class to load.
	 *
	 * @return Runnable
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	private function locate( string $class ) {
		if ( $this->container->has( $class ) ) {
			$runnable = $this->container->get( $class );
		}

		if ( ! $this->container->has( $class ) && class_exists( $class ) ) {
			$runnable = new $class();
		}

		if ( ! isset( $runnable ) ) {
			throw new \LogicException(
				sprintf(
					// Translators: %s: The class in question.
					__( '%s could not be loaded in the container or is not a class.', 'woominecraft' ),
					$class
				)
			);
		}

		if ( ! $runnable instanceof Runnable ) {
			throw new \LogicException(
				sprintf(
					// Translators: %s: The class in question.
					__( '%s is not a Runnable class, cannot continue.', 'woominecraft' ),
					$class
				)
			);
		}

		return $runnable;
	}
};
