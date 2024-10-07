<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveAddenda;

final class RemoveAddendaTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public function providerCleanDocumentWithAddenda(): array
    {
        return [
            'CFDI 3.3' => [
                'http://www.sat.gob.mx/cfd/3',
                <<<XML
                    <x:Comprobante xmlns:x="http://www.sat.gob.mx/cfd/3">
                    <x:Addenda>
                        <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar" />
                    </x:Addenda>
                    </x:Comprobante>
                    XML,
            ],
            'CFDI 4.0' => [
                'http://www.sat.gob.mx/cfd/4',
                <<<XML
                    <x:Comprobante xmlns:x="http://www.sat.gob.mx/cfd/4">
                    <x:Addenda>
                        <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar" />
                    </x:Addenda>
                    </x:Comprobante>
                    XML,
            ],
            'RET 2.0' => [
                'http://www.sat.gob.mx/esquemas/retencionpago/2',
                <<<XML
                    <x:Retenciones xmlns:x="http://www.sat.gob.mx/esquemas/retencionpago/2">
                    <x:Addenda>
                        <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar" />
                    </x:Addenda>
                    </x:Retenciones>
                    XML,
            ],
            'RET 1.0' => [
                'http://www.sat.gob.mx/esquemas/retencionpago/1',
                <<<XML
                    <x:Retenciones xmlns:x="http://www.sat.gob.mx/esquemas/retencionpago/1">
                    <x:Addenda>
                        <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar" />
                    </x:Addenda>
                    </x:Retenciones>
                    XML,
            ],
        ];
    }

    /** @dataProvider providerCleanDocumentWithAddenda */
    public function testCleanDocumentWithAddenda(string $namespace, string $source): void
    {
        $document = $this->createDocument($source);

        $cleaner = new RemoveAddenda();
        $cleaner->clean($document);

        $this->assertCount(
            0,
            $document->getElementsByTagNameNS($namespace, 'Addenda'),
            'Addenda element should not exists after cleaning',
        );
    }
}
