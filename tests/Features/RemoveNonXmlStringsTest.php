<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Document;
use PhpCfdi\CfdiCleaner\RemoveNonXmlStrings;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

class RemoveNonXmlStringsTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public function providerInputCases(): array
    {
        return [
            'nothing' => ['<a></a>', '<a></a>'],
            'utf-8 bom' => ['<a></a>', "\xEF\xBB\xBF<a></a>"],
            'content at begin' => ['<a></a>', 'begin<a></a>'],
            'content at end' => ['<a></a>', '<a></a>end'],
            'whitespaces and text' => ['<a></a>', "--foo\n \n\t<a></a>\n--bar\n"],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     * @dataProvider providerInputCases
     */
    public function testClean(string $expected, string $input): void
    {
        $document = Document::load($input);

        $cleaner = new RemoveNonXmlStrings();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document->getXmlContents());
    }
}
