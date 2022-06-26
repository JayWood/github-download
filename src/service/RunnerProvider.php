<?php
declare(strict_types=1);

namespace com\plugish\plugins\githubdownloads\service;

use com\plugish\plugins\githubdownloads\infrastructure\Runnable;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

class RunnerProvider implements ServiceProviderInterface {
	public function register( Container $pimple ) {
		$pimple[ RunnerProvider::class ] = static function( Container $c ): Runnable {
			return new HookRegistry(
				$c[ ContainerInterface::class ],
				$c['runnable'],
			);
		};
	}
}
