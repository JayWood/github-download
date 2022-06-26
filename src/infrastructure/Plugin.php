<?php
declare(strict_types=1);
namespace com\plugish\plugins\githubdownloads\infrastructure;

class Plugin implements Runnable {
	private Runnable $runnable;

	public function __construct( Runnable $runnable ) {
		$this->runnable = $runnable;
	}

	public function run() {
		$this->runnable->run();
	}
}
