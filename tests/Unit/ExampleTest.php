<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Unit;

use PhpCfdi\CfdiCleaner\Example;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class ExampleTest extends TestCase
{
    public function testAssertIsworking(): void
    {
        $example = new Example();
        $this->assertInstanceOf(Example::class, $example);
        $this->markTestSkipped('The unit test environment is working');
    }
}
