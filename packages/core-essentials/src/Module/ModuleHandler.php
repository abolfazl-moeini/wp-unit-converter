<?php

namespace BetterStudio\Core\Module;

use BetterStudio\Utils\Http;

/**
 * Implement this interface when a module need setup/ configuration before using.
 *
 * @since   1.0.0
 * @package BetterStudio\Core\Module
 * @format  Core Module
 */
abstract class ModuleHandler implements NeedSetup {

	use Singleton;

	/**
	 * Initialize the module.
	 *
	 * @return bool true on success.
	 */
	abstract public function init(): bool;

	/**
	 * Setup module.
	 *
	 * @return bool true on success.
	 * @since 1.0.0
	 */
	public static function setup(): bool {

		if ( ! function_exists( '\BetterStudio\Core\load_template' ) ) {

			require dirname( __DIR__ ) . '/functions.php';
		}

		$instance = static::instance();

		if ( $instance instanceof ShouldSaveData ) {

			array_map(
				[
					Http\Handlers\SaveRequestHandler::class,
					'register'
				],
				$instance->save_data_modules()
			);
		}

		return $instance->init();
	}

	/**
	 * Absolute path to the module directory
	 *
	 * @param string $path
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function path( string $path = '' ): string {

		$filename = ( new \ReflectionClass( static::class ) )->getFileName();

		return self::sanitize_path( dirname( $filename ) . $path );
	}

	/**
	 * Url to this the module directory
	 *
	 * @param string $path
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function url( string $path = '' ): string {

		$filename = ( new \ReflectionClass( static::class ) )->getFileName();

		$base_url = site_url(
			str_replace(
				[rtrim(ABSPATH,'/'),'\\'],
				['','/'],
				dirname( $filename )
			) );

		return self::sanitize_path( $base_url . $path );
	}

	/**
	 * @param string $template_name
	 * @param bool $return
	 *
	 * @return string
	 */
	public static function template( string $template_name, bool $return = true ): string {

		if ( $return ) {
			ob_start();
		}

		\BetterStudio\Core\load_template( $template_name, static::path( '/Templates/' ) );

		return $return ? ob_get_clean() : '';
	}

	/**
	 * Sanitize and remove double dots in path.
	 *
	 * @param string $path
	 *
	 * @since 1.0.7
	 * @return string
	 */
	protected static function sanitize_path( string $path ): string {

		if ( strpos( $path, '../' ) === false ) {

			return $path;
		}

		$replaced = preg_replace_callback( '#(\.{2}/) {1,} #isx', function ( $match ) {


			return sprintf( '{{R-%d}}', substr_count( $match[0], '../' ) ) . '/';
		}, $path );

		preg_match_all( '#(?P<dir>.*?)\{\{R\-(?P<level>\d+)\}\}#', "$replaced{{R-0}}", $matches, PREG_SET_ORDER );

		$result = '';

		foreach ( $matches as $match ) {

			if ( $match['level'] ) {

				$result .= dirname( $match['dir'], $match['level'] );
			} else {

				$result .= $match['dir'];
			}
		}

		return $result;
	}
}
