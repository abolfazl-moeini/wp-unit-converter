<?php

namespace UnitConverter;

function get_template( $template_names, array $args = [] ) {

	$args && set_template_variables( $args );

	ob_start();

	locate_template( $template_names );

	return ob_get_clean();
}

/**
 * Print errors list in html format.
 *
 * @param mixed $the_error
 *
 * @return string
 */
function render_errors( $the_error ): string {

	$errors = [];

	if ( is_wp_error( $the_error ) ) {

		/**
		 * @var \WP_Error $the_error
		 */
		foreach ( $the_error->get_error_codes() as $code ) {

			foreach ( $the_error->get_error_messages( $code ) as $message ) {

				$errors[] = compact( 'code', 'message' );
			}
		}
	} elseif ( $the_error instanceof \Throwable ) {

		/**
		 * @var \Throwable $the_error
		 */

		$errors [] = [
			'code'    => $the_error->getCode(),
			'message' => $the_error->getMessage(),
		];
	} elseif ( is_array( $the_error ) ) {

		$errors = $the_error;
	}

	return get_template( 'global/errors.php', compact( 'errors' ) );
}

function template_directory_name() {

	return 'UnitConverter-template';
}

/**
 * @return string
 * @since 1.0.0
 */
function template_directory_path() {

	return dirname( __DIR__ ) . '/templates';
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is
 *                                     false.
 *
 * @return string The template filename if one is located.
 * @since 1.0.0
 *
 * @see   locate_template for more doc
 *
 */
function locate_template( $template_names, bool $load = true, bool $require_once = false ): string {

	$scan_directories = [
		STYLESHEETPATH . '/' . template_directory_name() . '/',
		TEMPLATEPATH . '/' . template_directory_name() . '/',
		template_directory_path(),
	];

	$scan_directories = array_unique( array_filter( $scan_directories ) );

	foreach ( $scan_directories as $theme_directory ) {

		if ( $theme_file_path = load_templates( $template_names, $theme_directory, $load, $require_once ) ) {

			return $theme_file_path;
		}
	}

	return '';
}

/**
 * Require the template file.
 *
 * @param string|array $templates
 * @param string $theme_directory base directory. scan $templates files into this directory
 * @param bool $load
 * @param bool $require_once
 *
 * @return bool|string
 * @since 1.0.0
 *
 * @see   locate_template for parameters documentation
 *
 */
function load_templates( $templates, string $theme_directory, bool $load = false, bool $require_once = true ) {

	foreach ( (array) $templates as $theme_file ) {

		$theme_file      = ltrim( $theme_file, '/' );
		$theme_directory = trailingslashit( $theme_directory );

		if ( file_exists( $theme_directory . $theme_file ) ) {

			if ( $load ) {
				if ( $require_once ) {
					require_once $theme_directory . $theme_file;
				} else {
					require $theme_directory . $theme_file;
				}
			}

			return $theme_directory . $theme_file;
		}
	}

	return false;
}

$unit_converter_template_variables = [];

function clear_template_variables() {

	global $unit_converter_template_variables;

	$unit_converter_template_variables = [];
}

function set_template_variable( $var, $value ): void {

	global $unit_converter_template_variables;

	$unit_converter_template_variables[ $var ] = $value;
}

/**
 * @param string $key
 *
 * @return mixed
 */
function get_template_variable( string $key ) {

	global $unit_converter_template_variables;

	if ( isset( $unit_converter_template_variables[ $key ] ) ) {

		return $unit_converter_template_variables[ $key ];
	}
}

function set_template_variables( array $vars ): void {
	global $unit_converter_template_variables;

	$unit_converter_template_variables = array_merge( $unit_converter_template_variables ?? [], $vars );
}

function get_template_variables(): array {

	global $unit_converter_template_variables;

	return $unit_converter_template_variables ?? [];
}

/**
 * Url to plugin directory.
 *
 * @param string $path
 *
 * @return string
 * @since 1.0.0
 */
function plugin_url( string $path = '' ): string {

	return plugin_dir_url( __DIR__ ) . ltrim( $path, '/' );
}

/**
 * Absolute to plugin directory.
 *
 * @param string $path
 *
 * @return string
 * @since 1.0.0
 */
function plugin_path( string $path = '' ): string {

	return plugin_dir_path( __DIR__ ) . ltrim( $path, '/' );
}

/**
 * Url to assets directory.
 *
 * @param string $path
 *
 * @return string
 * @since 1.0.0
 */
function asset_url( string $path = '' ): string {

	return plugin_url( "assets/$path" );
}

/**
 * Absolute path to assets directory.
 *
 * @param string $path
 *
 * @return string
 * @since 1.0.0
 */
function asset_path( string $path = '' ): string {

	return plugin_path( "assets/$path" );
}
