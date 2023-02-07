<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RenameElementAddPrefix;
use PhpCfdi\CfdiCleaner\XmlStringCleaners\RemoveDuplicatedCfdi3Namespace;

class RemoveDuplicatedCfdi3NamespaceTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public function providerInputCases(): array
    {
        $xmlnsCfdi = 'xmlns:cfdi="http://www.sat.gob.mx/cfd/3"';
        $xmlns = 'xmlns="http://www.sat.gob.mx/cfd/3"';
        return [
            'at middle' => [
                "<cfdi:Comprobante $xmlnsCfdi/>",
                "<cfdi:Comprobante $xmlns $xmlnsCfdi/>",
            ],
            'multiple spaces' => [
                "<cfdi:Comprobante $xmlnsCfdi/>",
                "<cfdi:Comprobante \t $xmlns \r\n $xmlnsCfdi/>",
            ],
            'at end' => [
                "<cfdi:Comprobante $xmlnsCfdi />", // is replaced to a single space
                "<cfdi:Comprobante $xmlnsCfdi $xmlns/>",
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
        $cleaner = new RemoveDuplicatedCfdi3Namespace();

        $clean = @$cleaner->clean($input);
        $error = error_get_last() ?? [];

        $expectedErrorMessage = sprintf(
            'Class %s is deprecated, use %s',
            RemoveDuplicatedCfdi3Namespace::class,
            RenameElementAddPrefix::class
        );

        $this->assertSame(E_USER_DEPRECATED, intval($error['type'] ?? 0));
        $this->assertSame($expectedErrorMessage, strval($error['message'] ?? ''));

        $this->assertEquals($input, $clean);
    }
}
