<?php
/**
 * Base test case wiring up Brain Monkey's WordPress function stubs.
 *
 * @package Rekai
 */

namespace Rekai\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base test case for all unit tests in this suite.
 */
abstract class TestCase extends PHPUnitTestCase {

	/**
	 * Sets up Brain Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down Brain Monkey after each test.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}
}
