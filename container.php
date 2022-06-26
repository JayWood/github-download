<?php
declare(strict_types=1);
namespace com\plugish\plugins\githubdownloads;

use com\plugish\plugins\githubdownloads\infrastructure\Plugin;
use com\plugish\plugins\githubdownloads\service\RunnerProvider;
use Pimple\Container;
use Psr\Container\ContainerInterface;

$config = [
	Plugin::class => static function ( Container $c ): Plugin {
		return new Plugin( $c[ RunnerProvider::class ] );
	},

	ContainerInterface::class => static function ( Container $c ): ContainerInterface {
		return new \Pimple\Psr11\Container( $c );
	},

	'runnable' => [
		WooCommerce::class,
	],
];

$container = new Container( $config );
$container->register( new RunnerProvider() );

return new \Pimple\Psr11\Container( $container );
