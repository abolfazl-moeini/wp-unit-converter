<?php

namespace BetterStudioTest\Core\Shortcode;

use BetterStudio\Core\Shortcodes\ShortcodesSetup;
use BetterStudio\Core\Shortcodes\Shortcode;

class ShortcodesSetupTest extends \BetterStudioTest\TestCases\TestCase {

	function setUp() {

		parent::setUp();

		ShortcodesSetup::setup();
	}

	public function init_shortcodes() {

		ShortcodesSetup::instance()->append_shortcodes();
	}


	/**
	 * @test
	 * @covers ShortcodesSetup::register
	 * @covers ShortcodesSetup::shortcode_exists
	 */
	public function itShouldRegisterAShortcode() {

		ShortcodesSetup::register( 'sample', 'sample' );

		$this->assertTrue(
			ShortcodesSetup::shortcode_exists( 'sample' ),
			$this->method_working_notice( ShortcodesSetup::class, 'register' )
		);
	}

	/**
	 * @test
	 * @covers ShortcodesSetup::append_shortcodes
	 */
	public function registerShortcodesShouldAddNewWordPressShortcode() {
		global $shortcode_tags;

		ShortcodesSetup::register( 'sample', 'sampleCallback' );

		$this->init_shortcodes();;

		$this->assertTrue(
			isset( $shortcode_tags['sample'] ),
			$this->method_working_notice( ShortcodesSetup::class, 'append_shortcodes' )
		);
	}

	/**
	 * @test
	 */
	public function registeredShortcodeShouldFireTheCallback() {

		$mock = $this->createMock( Shortcode::class );

		$mock->expects( $this->once() )
		     ->method( 'render_shortcode' )
		     ->with(
			     $this->equalTo( [
				     'a' => 1,
				     'b' => 2,
			     ] ),

			     $this->equalTo( 'content' ),
			     $this->equalTo( 'sample' )
		     );

		ShortcodesSetup::register( 'sample', $mock );

		$this->init_shortcodes();

		do_shortcode( '[sample a="1" b="2"]content[/sample]' );
	}

	/**
	 * @test
	 */
	public function registeredShortcodeShouldFireTheCallbackWhenRegisterClassName() {
		global $mock;

		$mock = $this->createMock( Shortcode::class );

		$mock->expects( $this->once() )
		     ->method( 'render_shortcode' )
		     ->with(
			     $this->equalTo( [
				     'a' => 1,
				     'b' => 2,
			     ] ),

			     $this->equalTo( 'content' ),
			     $this->equalTo( 'sample' )
		     );

		ShortcodesSetup::register( 'sample', SampleShortcode::class );

		$this->init_shortcodes();

		do_shortcode( '[sample a="1" b="2"]content[/sample]' );
	}
}
