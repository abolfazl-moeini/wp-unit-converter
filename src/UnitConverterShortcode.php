<?php

namespace UnitConverter;

use BetterStudio\Core\Shortcodes;

class UnitConverterShortcode extends Shortcodes\Shortcode {

	const SHORTCODE = 'unit-converter';

	/**
	 * @inheritDoc
	 *
	 */
	public function render_shortcode( array $attributes, string $content, string $tag ): string {

		$template = $attributes['theme'] ?? 'minimal';

		/**
		 * TODO: Refactor this
		 */
		wp_enqueue_style( 'wp-unit-converter-' . $template,
			\WPUnitConverterPlugin::url( sprintf( '/Templates/%s/style.css', $template ) ) );
		wp_enqueue_script( 'wp-unit-converter-' . $template,
			\WPUnitConverterPlugin::url( sprintf( '/Templates/%s/script.js', $template ) ),
			[ 'wp-api-fetch', 'wp-i18n' ]
		);

		wp_localize_script( 'wp-unit-converter-' . $template, 'WP_Unit_Converter', [
			'categories' => \WPUnitConverterPlugin::instance()->unit_categories(),
		] );

		return \WPUnitConverterPlugin::template(
			sprintf( '%s/widget.php', $template )
		);
	}

}
