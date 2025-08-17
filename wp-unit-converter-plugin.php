<?php

use BetterStudio\Core\{
	Shortcodes,
	Module,
};

class WPUnitConverterPlugin extends Module\ModuleHandler {

	public function init(): bool {

		$this->core_modules();
		// $this->sub_modules();
		$this->init_hooks();
		$this->register_shortcodes();

		return true;
	}

	public function sub_modules(): void {
	}

	public function register_shortcodes(): void {

		Shortcodes\ShortcodesSetup::register( \UnitConverter\UnitConverterShortcode::SHORTCODE,
			\UnitConverter\UnitConverterShortcode::class );
	}

	public function core_modules(): void {

		Shortcodes\ShortcodesSetup::setup();
	}

	public function init_hooks(): void {

		add_action( 'plugins_loaded', [ $this, 'load_plugin_text_domain' ], 99 );
		add_action( 'rest_api_init', [ $this, 'init_rest' ] );
	}

	public function load_plugin_text_domain(): void {

//		load_plugin_textdomain( 'unit-converter', false, dirname( plugin_basename( __DIR__ ) ) . '/languages/' );
	}

	public function init_rest(): void {

		register_rest_route( 'unit-converter/v1', '/convert', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'handle_conversion' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'category' => [ 'required' => true, 'type' => 'string' ],
				'from'     => [ 'required' => true, 'type' => 'string' ],
				'to'       => [ 'required' => true, 'type' => 'string' ],
				'value'    => [ 'required' => true, 'type' => 'number' ],
			],
		] );
	}


	public function handle_conversion( WP_REST_Request $request ) {
		$category = $request['category'];
		$from     = $request['from'];
		$to       = $request['to'];
		$value    = (float) $request['value'];

		$result = $this->perform_conversion( $category, $from, $to, $value );

		if ( $result === false ) {
			return new WP_Error( 'invalid', __( 'Invalid conversion', 'unit-converter' ), [ 'status' => 400 ] );
		}

		return [ 'result' => $result ];
	}


	function perform_conversion( $category, $from, $to, $value ) {

		if ( $category === 'length' ) {
			$to_m = [
				'm'  => 1,
				'cm' => 0.01,
				'in' => 0.0254,
				'yd' => 0.9144,
				'ft' => 0.3048,
				'mi' => 1609.34,
				'km' => 1000
			];
			if ( ! isset( $to_m[ $from ], $to_m[ $to ] ) ) {
				return false;
			}

			return $value * ( $to_m[ $from ] / $to_m[ $to ] );
		}

		return false;
	}


	public function unit_categories(): array {

		$icon_path = self::url( '/assets/icons/' );

		return [
			'length'      => [
				'name'  => __( 'Length', 'unit-converter' ),
				'icon'  => $icon_path . 'length.svg',
				'units' => [
					'm'  => 'Meters',
					'cm' => 'Centimeters',
					'in' => 'Inches',
					'yd' => 'Yards',
					'km' => 'Kilometers',
					'mi' => 'Miles',
				],
			],
			'speed'       => [
				'name'  => __( 'Speed', 'unit-converter' ),
				'icon'  => $icon_path . 'speed.svg',
				'units' => [
					'm/s'  => 'Meters/sec',
					'km/h' => 'Kilometers/hr',
					'mph'  => 'Miles/hr',
				]
			],
			'temperature' => [
				'name'  => __( 'Temperature', 'unit-converter' ),
				'icon'  => $icon_path . 'temperature.svg',
				'units' => [
					'C' => 'Celsius',
					'F' => 'Fahrenheit',
					'K' => 'Kelvin',
				],
			],
			'volume'      => [
				'name'  => __( 'Volume', 'unit-converter' ),
				'icon'  => $icon_path . 'volume.svg',
				'units' => [
					'l'   => 'Liters',
					'ml'  => 'Milliliters',
					'gal' => 'Gallons (US)',
					'm3'  => 'Cubic Meters',
				],
			],
			'weight'      => [
				'name'  => __( 'Weight', 'unit-converter' ),
				'icon'  => $icon_path . 'weight.svg',
				'units' => [
					'kg' => 'Kilograms',
					'g'  => 'Grams',
					'lb' => 'Pounds',
					'oz' => 'Ounces',
				],
			]
		];
	}
}
