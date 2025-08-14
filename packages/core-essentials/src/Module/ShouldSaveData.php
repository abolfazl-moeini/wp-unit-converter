<?php

namespace BetterStudio\Core\Module;

use BetterStudio\Utils\Http;

interface ShouldSaveData {

	/**
	 * List of the submodule that have to save data.
	 *
	 * @since 1.0.0
	 * @return Http\Contracts\ShouldSaveData[]
	 */
	public function save_data_modules()
	: array;
}
