<?php

namespace BetterStudio\Core\Module;

trait UseOptions {

	/**
	 * Store the custom options.
	 *
	 * @since 1.0.0
	 * @var mixed
	 */
	protected $options;

	/**
	 * Get/Set custom options.
	 *
	 * @param mixed $options
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function options( $options = null ) {

		if ( isset( $options ) ) {
			$this->options = $options;
		}

		return $this->options;
	}
}
