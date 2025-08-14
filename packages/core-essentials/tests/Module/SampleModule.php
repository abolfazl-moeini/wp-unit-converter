<?php

namespace BetterStudioTest\Core\Module;

use BetterStudio\Core\Module;
use BetterStudio\Utils\Http;

class SampleModule extends Module\ModuleHandler implements Module\ShouldSaveData {

	/**
	 * @var array
	 */
	protected static $config;

	public function init(): bool {

		return true;
	}

	public function save_data_modules(): array {

		return [
			SampleSubModule::instance()
		];
	}
}
