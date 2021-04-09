<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlStringCleaners\XmlNsSchemaLocation;

class RepairXmlNsSchemaLocationTest extends TestCase
{
    /** @return array<array{string, string}> */
    public function providerInputCases(): array
    {
        return [
            'spaces' => [
                '<root xsi:schemaLocation="http://localhost/a http://localhost/a.xsd"/>',
                '<root xmlns:schemaLocation="http://localhost/a http://localhost/a.xsd"/>',
            ],
            'tabs' => [
                "<root\txsi:schemaLocation=\"http://localhost/a http://localhost/a.xsd\"/>",
                "<root\txmlns:schemaLocation=\"http://localhost/a http://localhost/a.xsd\"/>",
            ],
            'line feed' => [
                "<root\nxsi:schemaLocation=\"http://localhost/a http://localhost/a.xsd\"/>",
                "<root\nxmlns:schemaLocation=\"http://localhost/a http://localhost/a.xsd\"/>",
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     * @dataProvider providerInputCases
     */
    public function testCleaning(string $expected, string $input): void
    {
        $cleaner = new XmlNsSchemaLocation();
        $clean = $cleaner->clean($input);

        $this->assertEquals($expected, $clean);
    }
}
