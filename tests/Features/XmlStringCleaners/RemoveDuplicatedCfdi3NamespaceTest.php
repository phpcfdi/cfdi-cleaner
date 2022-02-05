<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
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
        $this->expectDeprecation();
        $clean = $cleaner->clean($input);

        $this->assertEquals($input, $clean);
    }
}
