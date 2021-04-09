<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\AppendXmlDeclaration;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

class AddXmlDeclarationTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public function providerInputCases(): array
    {
        return [
            'skip xml with header' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
            ],
            'add to xml without header' => [
                "<?xml version=\"1.0\"?>\n<root/>",
                '<root/>',
            ],
            'add to non xml' => [
                "<?xml version=\"1.0\"?>\nfoo",
                'foo',
            ],
        ];
    }

    /**
     * @param string $expected
     * @param string $input
     * @dataProvider providerInputCases
     */
    public function testClean(string $expected, string $input): void
    {
        $cleaner = new AppendXmlDeclaration();
        $clean = $cleaner->clean($input);

        $this->assertEquals($expected, $clean);
    }
}
