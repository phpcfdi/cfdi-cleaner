<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Document;
use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlNsSchemaLocation;

class RepairXmlNsSchemaLocationTest extends TestCase
{
    /** @return array<array{string, string}> */
    public function providerInputCases(): array
    {
        return [
            'spaces' => [
                '<root xsi:schemaLocation="http://a/a http://a/a.xsd"/>',
                '<root xmlns:schemaLocation="http://a/a http://a/a.xsd"/>',
            ],
            'tabs' => [
                "<root\txsi:schemaLocation=\"http://a/a http://a/a.xsd\"/>",
                "<root\txmlns:schemaLocation=\"http://a/a http://a/a.xsd\"/>",
            ],
            'line feed' => [
                "<root\nxsi:schemaLocation=\"http://a/a http://a/a.xsd\"/>",
                "<root\nxmlns:schemaLocation=\"http://a/a http://a/a.xsd\"/>",
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
        $document = Document::load($input);

        $cleaner = new XmlNsSchemaLocation();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document->getXmlContents());
    }
}
