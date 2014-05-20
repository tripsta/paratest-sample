<?php

class FileTest extends PHPUnit_Framework_TestCase
{
	protected $_filename;

	function setUp()
	{
		parent::setUp();
		$this->_filename = sprintf('out%s.txt', getenv('TEST_TOKEN'));
	}

	function test01()
	{
		$this->_testCreateRandomContent();
	}

	function test02()
	{
		$this->_testCreateRandomContent();
	}

	function test03()
	{
		$this->_testCreateRandomContent();
	}

	function test04()
	{
		$this->_testCreateRandomContent();
	}

	function test05()
	{
		$this->_testCreateRandomContent();
	}

	function test06()
	{
		$this->_testCreateRandomContent();
	}

	function test07()
	{
		$this->_testCreateRandomContent();
	}

	function test08()
	{
		$this->_testCreateRandomContent();
	}

	function test09()
	{
		$this->_testCreateRandomContent();
	}

	function test10()
	{
		$this->_testCreateRandomContent();
	}

	function test11()
	{
		$this->_testCreateRandomContent();
	}

	function test12()
	{
		$this->_testCreateRandomContent();
	}

	function test13()
	{
		$this->_testCreateRandomContent();
	}

	function test14()
	{
		$this->_testCreateRandomContent();
	}

	function test15()
	{
		$this->_testCreateRandomContent();
	}

	function test16()
	{
		$this->_testCreateRandomContent();
	}

	function test17()
	{
		$this->_testCreateRandomContent();
	}

	function test18()
	{
		$this->_testCreateRandomContent();
	}

	function test19()
	{
		$this->_testCreateRandomContent();
	}

	function test20()
	{
		$this->_testCreateRandomContent();
	}

	function _testCreateRandomContent()
	{
		$text = sprintf("here is a random number %s", rand(1,1000));
		file_put_contents($this->_filename, $text);
		usleep(200000);
		$outputText = file_get_contents($this->_filename);
		$this->assertEquals($text, $outputText);
	}
}
