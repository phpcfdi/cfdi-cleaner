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
        $input = '<root xmlns:schemaLocation="http://a/a http://a.a/a.xsd"/>';
        $expected = '<root xsi:schemaLocation="http://a/a http://a.a/a.xsd"/>';
        return [
            'spaces' => [$expected, $input],
            'tabs' => [$expected, str_replace("\txmlns", ' xmlns', $input)],
            'line feed' => [$expected, str_replace("\nxmlns", ' xmlns', $input)],
        ];
    }

    /**
     * @dataProvider providerInputCases
     * @param string $expected
     * @param string $input
     */
    public function testCleaning(string $expected, string $input): void
    {
        $document = Document::load($input);

        $cleaner = new XmlNsSchemaLocation();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document->getXmlContents());
    }
}
