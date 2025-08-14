<?php

namespace BetterStudioTest\Core\Module;

use BetterStudio\Core;

class functionsTest extends \BetterStudioTest\TestCases\TestCase {

	/**
	 * @test
	 */
	public function testSliceIdFunction() {

		$this->assertNull( Core\slice_id( [ 1, 2, 4 ] ) );

		$id = 'abcd';

		$this->assertEquals( $id, Core\slice_id( [ 1, 'id' => $id, 4 ] ) );
	}

	/**
	 * @test
	 */
	public function testSetTemplateVariable() {
		global $bs_template_vars;

		Core\set_template_variable( 'name', '1234' );

		$this->assertEquals(
			'1234',
			$bs_template_vars['name']
		);
	}


	/**
	 * @test
	 */
	public function testGetTemplateVariable() {

		Core\set_template_variable( 'name', '1234' );

		$this->assertEquals(
			'1234',
			Core\get_template_variable( 'name' )
		);
	}

	/**
	 * @test
	 */
	public function testSetTemplateVariables() {
		global $bs_template_vars;

		$vars = [
			'name'   => 'publisher',
			'type'   => 'theme',
			'author' => 'better',
		];

		Core\set_template_variables( $vars );

		$this->assertEquals(
			$vars,
			$bs_template_vars
		);
	}

	/**
	 * @test
	 */
	public function testGetTemplateVariables() {

		$vars = [
			'name'   => 'publisher',
			'type'   => 'theme',
			'author' => 'better',
		];

		Core\set_template_variables( $vars );

		$this->assertEquals(
			$vars,
			Core\get_template_variables()
		);
	}

	/**
	 * @test
	 */
	public function testLoadTemplate() {

		$mock = $this->getMockBuilder( \stdClass::class )
		             ->setMethods( [ 'fired' ] )
		             ->getMock();

		$mock->expects( $this->once() )
		     ->method( 'fired' );

		Core\set_template_variable( 'mock', $mock );

		$this->assertTrue(
			Core\load_template( 'sample-template.php', __DIR__ )
		);

		$this->assertFalse( Core\load_template( 'wrong-file', __DIR__ ) );
	}
}
