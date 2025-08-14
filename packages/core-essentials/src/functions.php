<?php

namespace BetterStudio\Core;

/**
 * Helper function to get id index of an array.
 *
 * @param array $array
 *
 * @since 1.0.0
 * @return mixed
 */
function slice_id( $array ) {

	return isset( $array['id'] ) ? $array['id'] : null;
}

/**
 * @param string $name
 * @param mixed  $value
 *
 * @since 1.0.0
 * @global array $bs_template_vars Temp storage
 *
 */
function set_template_variable( string $name, $value ) {

	global $bs_template_vars;

	$bs_template_vars[ $name ] = $value;
}

/**
 * @param string $name
 *
 * @since 1.0.0
 * @return mixed
 * @global array $bs_template_vars Temp storage
 */
function get_template_variable( string $name ) {
	global $bs_template_vars;

	return $bs_template_vars[ $name ] ?? null;
}


/**
 * @param array &$vars
 *
 * @since 1.0.0
 * @global array $bs_template_vars Temp storage
 *
 */
function set_template_variables( array &$vars ) {

	global $bs_template_vars;

	$bs_template_vars = $vars;
}

/**
 * @since 1.0.0
 * @return array
 */
function get_template_variables(): array {

	global $bs_template_vars;

	return $bs_template_vars ?? [];
}

/**
 *
 * @param string $template
 * @param string $directory
 *
 * @since 1.0.0
 * @return bool true on success.
 */
function load_template( $template, $directory ): bool {

	$path = trailingslashit( $directory ) . $template;

	if ( ! is_readable( $path ) ) {

		return false;
	}

	include $path;

	return true;
}


/**
 * Print messages list in html format.
 *
 * @param mixed $the_error
 *
 * @return string
 */
function render_messages( $the_error ): string {

	$errors = [];

	if ( is_wp_error( $the_error ) ) {

		/**
		 * @var \WP_Error $the_error
		 */
		foreach ( $the_error->get_error_codes() as $code ) {

			foreach ( $the_error->get_error_messages( $code ) as $message ) {

				$type = $the_error->get_error_data( $code );

				$errors[] = compact( 'code', 'message', 'type' );
			}
		}
	} elseif ( $the_error instanceof \Exception ) {

		$errors[] = [
			'code'    => $the_error->getCode(),
			'message' => $the_error->getMessage(),
			'type'    => is_callable( [ $the_error, 'getType' ] ) ? $the_error->getType() : 'error',
		];

	} elseif ( is_array( $the_error ) ) {

		if ( ! isset( $the_error[0] ) ) {

			$the_error = [ $the_error ];
		}

		$errors = $the_error;

	} else if ( is_string( $the_error ) ) {

		$errors = [
			[
				'code'    => 'warning',
				'message' => $the_error,
				'type'    => 'warning',
			]
		];
	}

	set_template_variable( 'messages', $errors );

	ob_start();

	load_template( 'messages.php', __DIR__ . '/Templates' );

	return ob_get_clean();
}

/**
 * Terminates execution of the script
 *
 * @since 1.0.0
 */
function bs_exit() {

	if ( apply_filters( 'BetterStudio/Exit', true ) ) {

		exit;
	}
}

/**
 * @param int $total
 * @param int $current
 * @param int $per_page
 *
 * @since 1.0.0
 * @return array
 */
function pagination_response( $total, $current, $per_page ) {

	$items   = [];
	$current = intval( $current );

	{  # Items

		$pages = ceil( $total / $per_page );

		for ( $i = 1; $i <= $pages; $i ++ ) {

			$items[] = [
				'number' => $i,
				'active' => $i === $current,
			];
		}

		$items = array_slice( $items, 0, 20 ); // TODO: add support for page>20
	}


	return [
		'havePrev' => $current > 1,
		'haveNext' => $current < end( $items )['number'],
		'exists'   => $total > $per_page,
		'total'    => $total,
		'items'    => $items,
		'labels'   => [

			'prev' => _x( 'Prev', 'Pagination', 'betterstudio' ),
			'next' => _x( 'Next', 'Pagination', 'betterstudio' ),
		]
	];
}
