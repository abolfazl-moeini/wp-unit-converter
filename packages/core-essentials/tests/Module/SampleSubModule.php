<?php

namespace BetterStudioTest\Core\Module;

use BetterStudio\Core\Module;
use BetterStudio\Utils\Http;

class SampleSubModule extends Module\ModuleHandler implements Http\Contracts\ShouldSaveData {

	public function init(): bool {

		return true;
	}
	/**
	 * @inheritDoc
	 */
	public function save_hook()
	: string {

		return 'hook';
	}

	/**
	 * @inheritDoc
	 */
	public function save_data( Http\HttpRequest $request, ...$params )
	: bool {

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function save_permission()
	: bool {

		return true;
	}
}
