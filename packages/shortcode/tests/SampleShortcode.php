<?php

namespace BetterStudioTest\Core\Shortcode;

use BetterStudio\Core\Shortcodes;

class SampleShortcode extends Shortcodes\Shortcode {

	/**
	 * @inheritDoc
	 *
	 */
	public function render_shortcode( array $attributes, string $content, string $tag )
	: string {

		global $mock;

		return call_user_func_array( [ $mock, 'render_shortcode' ], func_get_args() );
	}
}