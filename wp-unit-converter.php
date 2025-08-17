<?php
/**
 * Plugin Name:       Unit Converter Pro
 * Description:       A modern unit converter implemented via a shortcode and the REST API.
 * Version:           1.0.0
 * Text Domain:       wp-unit-converter
 * Domain Path:       /languages
 */


require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/wp-unit-converter-plugin.php';
require __DIR__ . '/src/helper-functions.php';

( new WPUnitConverterPlugin() )->init();
