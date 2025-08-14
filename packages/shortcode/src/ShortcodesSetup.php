<?php

namespace BetterStudio\Core\Shortcodes;

use BetterStudio\Core;

class ShortcodesSetup extends Core\Module\ModuleHandler {

	/**
	 * Store the module configuration array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected static $config = [];


	/**
	 * Store list of shortcodes to register.
	 *
	 * @since 1.0.0
	 * @var array
	 *
	 */
	protected static $shortcodes = [];

	/**
	 * Initialize.
	 *
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function init()
	: bool {

		add_action( 'template_redirect', [ $this, 'append_shortcodes' ], 99 );

		return true;
	}


	/**
	 * Register new shortcode.
	 *
	 * @param string           $shortcode     the shortcode name.
	 * @param string|Shortcode $handler_class shortcode class
	 *
	 * @since 1.0.0
	 * @return bool true on success.
	 */
	public static function register( string $shortcode, $handler_class ) {

		static::$shortcodes[ $shortcode ] = $handler_class;

		return true;
	}

	/**
	 * Whether to check if shortcode exists.
	 *
	 * @param string $shortcode
	 *
	 * @return bool
	 */
	public static function shortcode_exists( $shortcode ) {

		return isset( static::$shortcodes[ $shortcode ] );
	}

	/**
	 * Introduce registered shortcodes to WordPress.
	 *
	 * @hooked init
	 *
	 * @since  1.0.0
	 */
	public function append_shortcodes() {

		foreach ( static::$shortcodes as $shortcode => $handler_class ) {

			add_shortcode( $shortcode, [ $this, 'render_shortcode' ] );
		}
	}

	/**
	 * Render the shortcode
	 *
	 * @param array  $attributes
	 * @param string $content
	 * @param string $shortcode
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function render_shortcode( $attributes, $content, $shortcode ) {

		if ( empty( static::$shortcodes[ $shortcode ] ) ) {

			return '';
		}

		if ( is_string( static::$shortcodes[ $shortcode ] ) ) {

			$handler = call_user_func( [ static::$shortcodes[ $shortcode ], 'instance' ] );

		} else {

			$handler = static::$shortcodes[ $shortcode ];
		}

		if ( ! is_array( $attributes ) ) {

			$attributes = [];
		}

		return $handler->render_shortcode( $attributes, $content, $shortcode );
	}
}
