<?php

namespace BetterStudio\Core\Module;

/**
 * Implement this interface when a module need setup/ configuration before using.
 *
 * @since   1.0.0
 * @package BetterStudio\Core\Module
 * @format  Core Module
 */
interface NeedSetup {

	/**
	 * Setup module.
	 *
	 * @since 1.0.0
	 * @return bool true on success.
	 */
	public static function setup()
	: bool;
}
