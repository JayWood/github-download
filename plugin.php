<?php
declare(strict_types=1);
/**
 * Plugin Name: Github Downloads
 * Plugin URI: https://plugish.com
 * Description: Allows.
 * Author: JayWood
 * Author URI: https://plugish.com/
 * Version: 2.0.0
 * License: GPLv2
 * Text Domain: woominecraft
 * Domain Path: /languages
 * Author URI: http://plugish.com
 * WC requires at least: 3.0
 * WC tested up to: 6.0.0
 */

namespace com\plugish\plugins\githubdownloads;

use com\plugish\plugins\githubdownloads\infrastructure\Plugin;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/vendor/autoload.php';

function container(): ContainerInterface {
	static $container;

	if ( ! $container ) {
		$container = require __DIR__ . '/container.php';
	}

	return $container;
}

add_action(
	'plugins_loaded',
	static function(): void {
		container()->get( Plugin::class )->run();
	}
);
