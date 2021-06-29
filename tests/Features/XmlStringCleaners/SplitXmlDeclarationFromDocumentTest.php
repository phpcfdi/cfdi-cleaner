<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlStringCleaners\SplitXmlDeclarationFromDocument;

final class SplitXmlDeclarationFromDocumentTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public function providerInputCases(): array
    {
        return [
            'doc on line 1 no white space' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
                '<?xml version="1.0" encoding="UTF-8"?><root/>',
            ],
            'doc on line 1' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
                '<?xml version="1.0" encoding="UTF-8"?> <root/>',
            ],
            'doc on line 2' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?> \n <root/>",
            ],
            'doc on line 3' => [
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root/>",
                "<?xml version=\"1.0\" encoding=\"UTF-8\"?> \n \n <root/>",
            ],
            'no declaration' => [
                '<root/>',
                '<root/>',
            ],
            'no encoding declaration + doc on line 2' => [
                "<?xml version=\"1.0\"?>\n<root/>",
                "<?xml version=\"1.0\"?> \n <root/>",
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
        $cleaner = new SplitXmlDeclarationFromDocument();
        $clean = $cleaner->clean($input);

        $this->assertEquals($expected, $clean);
    }
}
