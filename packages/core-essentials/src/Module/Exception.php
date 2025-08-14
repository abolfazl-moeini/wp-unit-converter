<?php

namespace BetterStudio\Core\Module;

/**
 * Custom Exception except error code as string
 */
Class Exception extends \Exception {

	/**
	 * Store the message type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $type;

	public function __construct( $message = '', $code = '', string $type = 'error' ) {

		parent::__construct( $message, 0 );

		$this->code = $code ? $code : 'error';
		$this->type = $type;
	}

	/**
	 * Get the message type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function getType()
	: string {

		return $this->type;
	}
}
