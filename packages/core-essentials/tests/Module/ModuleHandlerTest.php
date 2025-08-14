<?php

namespace BetterStudioTest\Core\Module;

use BetterStudio\Core\Module;
use BetterStudio\Utils\Http;

class ModuleHandlerTest extends \BetterStudioTest\TestCases\TestCase {

	protected static $config;

	function setUp():void {

		parent::setUp();

		SampleModule::setup();
	}

	/**
	 * @test
	 */
	public function templateMethodShouldReadFilesFromOwnModuleTemplateDirectory() {

		$this->assertEquals(
			'template-a-content',
			SampleModule::template('template-a.php')
		);
	}

	/**
	 * @test
	 */
	public function theUrlMethodShouldGiveTheCorrectUrl() {

		$url = site_url( str_replace( ABSPATH, '', __DIR__ ) );

		$this->assertEquals(
			$url,
			SampleModule::url(),
			$this->method_working_notice( Setup::class, 'url', '1' )
		);

		$this->assertEquals(
			$url . '/append/following/path',
			SampleModule::url( '/append/following/path' ),
			$this->method_working_notice( Setup::class, 'url', '2' )
		);

		$this->assertEquals(
			$url . '/?q=s&s=v',
			SampleModule::url( '/?q=s&s=v' ),
			$this->method_working_notice( Setup::class, 'url', '3' )
		);
	}

	/**
	 * @test
	 */
	public function theUrlMethodShouldHandleBackward() {


		$this->assertEquals(
			site_url( str_replace( ABSPATH, '', dirname(__DIR__) . '/a/b' ) ),
			SampleModule::url('../a/b'),
			$this->method_working_notice( Setup::class, 'url', '1' )
		);

		$this->assertEquals(
			site_url( str_replace( ABSPATH, '', dirname(__DIR__) . '/a/b' ) ),
			SampleModule::url('/../a/b'),
			$this->method_working_notice( Setup::class, 'url', '1' )
		);

		$this->assertEquals(
			site_url( str_replace( ABSPATH, '', dirname(__DIR__) . '/a/b/c/x/?x=1' ) ),
			SampleModule::url('../a/b/c/d/e/../../x/?x=1'),
			$this->method_working_notice( Setup::class, 'url', '1' )
		);
	}

	/**
	 * @test
	 */
	public function thePathMethodShouldHandleBackward() {


		$this->assertEquals(
			dirname(__DIR__). '/a/b',
			SampleModule::path('../a/b'),
			$this->method_working_notice( Setup::class, 'path', '1' )
		);


		$this->assertEquals(
			dirname(__DIR__). '/a/b',
			SampleModule::path('/../a/b'),
			$this->method_working_notice( Setup::class, 'path', '1' )
		);


		$this->assertEquals(
			dirname(__DIR__). '/a/b/c/x/',
			SampleModule::path('../a/b/c/d/e/../../x/'),
			$this->method_working_notice( Setup::class, 'path', '1' )
		);

	}



	/**
	 * @test
	 */
	public function itShouldRegisterModuleClassesWhenTheyNeedToSaveData() {

		$modules = Http\Handlers\SaveRequestHandler::modules();

		$this->assertSame(
			SampleSubModule::instance(),
			array_shift( $modules ),

			'ModuleHandler Should register submodule to SaveRequestHandler stack.'
		);
	}
}
