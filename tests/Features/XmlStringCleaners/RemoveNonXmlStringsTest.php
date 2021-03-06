<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlStringCleaners\RemoveNonXmlStrings;

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
            'ltgt empty' => ['<>', '<>'],
            'ltgt lead and trail' => ['<>', '_<>_'],
            'out of order string' => ['', '_>_<_'],
            'no xml' => ['< b && b >', 'a < b && b > c'],
            'without lt or gt' => ['', 'a b c'],
            'without gt ' => ['', 'a < b c'],
            'without lt ' => ['', 'a b > c'],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     * @dataProvider providerInputCases
     */
    public function testClean(string $expected, string $input): void
    {
        $cleaner = new RemoveNonXmlStrings();
        $clean = $cleaner->clean($input);

        $this->assertEquals($expected, $clean);
    }
}
