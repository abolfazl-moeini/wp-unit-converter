<?php

namespace BetterStudio\Core\Module;

/**
 * Trait Singleton will implements get singleton instance method.
 *
 * @since   1.0.0
 * @package BetterStudio\Core\Module
 * @format  Core Module
 */
Trait Singleton {

	/**
	 * Store an instance of the class.
	 *
	 * @since 1.0.0
	 * @var static
	 */
	protected static $instance;


	/**
	 * Get singleton instance of the class.
	 *
	 * @codeCoverageIgnore
	 *
	 * @since 1.0.0
	 * @return static
	 */
	public static function instance() {

		if ( ! static::$instance instanceof static ) {

			static::$instance = new static();
		}

		return static::$instance;
	}
}
