<?php

namespace BetterStudio\Core\Shortcodes;

use BetterStudio\Core\Module;

abstract class Shortcode {

	use Module\Singleton;

	/**
	 * Render shortcode html output.
	 *
	 * @param array  $attributes
	 * @param string $content
	 * @param string $tag
	 *
	 * @since 1.0.0
	 * @return string
	 */
	abstract public function render_shortcode( array $attributes, string $content, string $tag )
	: string;
}