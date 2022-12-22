<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Unit;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DOMDocument;
use PhpCfdi\CfdiCleaner\ExcludeList;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class ExcludeListTest extends TestCase
{
    public function testConstructorWithEmptyList(): void
    {
        $excludeList = new ExcludeList();
        $this->assertTrue($excludeList->isEmpty());
        $this->assertSame([], iterator_to_array($excludeList));
    }

    public function testConstructorWithValues(): void
    {
        $classes = [
            DateTime::class,
            DateTimeImmutable::class,
        ];

        $excludeList = new ExcludeList(...$classes);

        $this->assertFalse($excludeList->isEmpty());
        $this->assertSame($classes, iterator_to_array($excludeList));
    }

    public function testMatch(): void
    {
        $excludeList = new ExcludeList(DateTimeInterface::class);
        $this->assertTrue($excludeList->match(new DateTime()));
        $this->assertTrue($excludeList->match(new DateTimeImmutable()));
        $this->assertTrue($excludeList->match($this->createMock(DateTime::class)));
        $this->assertFalse($excludeList->match((object) []));
    }

    public function testFilter(): void
    {
        $expected = [];
        $objects = [
            new DateTime(),
            $expected[] = (object) [],
            new DateTimeImmutable(),
            $expected[] = new DOMDocument(),
        ];

        $excludeList = new ExcludeList(DateTimeInterface::class);
        $filtered = $excludeList->filterObjects(...$objects);

        $this->assertSame($expected, array_values($filtered));
    }
}
