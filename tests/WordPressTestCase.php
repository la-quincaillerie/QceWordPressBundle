<?php

namespace Qce\WordPressBundle\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;

class WordPressTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    protected function createMockFunction(string $functionName): Monkey\Expectation\Expectation
    {
        return Monkey\Functions\expect($functionName);
    }
}
