<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveAddenda;

final class RemoveAddendaTest extends TestCase
{
    public function testCleanDocumentWithAddenda(): void
    {
        $document = $this->createDocument(<<< XML
            <x:Comprobante xmlns:x="http://www.sat.gob.mx/cfd/3">
            <x:Addenda>
                <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar" />
            </x:Addenda>
            </x:Comprobante>
            XML
        );

        $cleaner = new RemoveAddenda();
        $cleaner->clean($document);

        $this->assertCount(
            0,
            $document->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Addenda'),
            'Addenda element should not exists after cleaning'
        );
    }
}
