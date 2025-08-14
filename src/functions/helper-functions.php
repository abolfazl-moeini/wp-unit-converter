<?php

namespace UnitConverter;


function pluck_keys( array &$array, ...$keys ): array {

	return array_diff_key( $array, array_flip( $keys ) );
}

function asset_info( string $file_path, $debug = false ): array {

	if ( ! preg_match( '#(.+).(?:js|css)$#', $file_path, $match ) ) {

		return [];
	}

	$asset_file = $match[1] . '.asset.php';

	if ( ! file_exists( $asset_file ) ) {

		return [];
	}

	return include $asset_file;
}
