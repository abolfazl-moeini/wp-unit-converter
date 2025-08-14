<?php
/**
 * Plugin Name:       Unit Converter Pro
 * Description:       A modern unit converter implemented via a shortcode and the REST API.
 * Version:           1.0.0
 * Text Domain:       wp-unit-converter
 * Domain Path:       /languages
 */


require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/src/functions/template-functions.php';
require __DIR__ . '/src/functions/helper-functions.php';

( new UnitConverter\PluginSetup() )->init();
