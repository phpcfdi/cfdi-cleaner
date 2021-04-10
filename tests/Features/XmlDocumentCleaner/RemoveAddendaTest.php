<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveAddenda;

final class RemoveAddendaTest extends TestCase
{
    public function testCleanDocumentWithAddenda(): void
    {
        $input = /** @lang text */ <<< XML
            <?xml version="1.0" encoding="UTF-8"?>
            <x:Comprobante xmlns:x="http://www.sat.gob.mx/cfd/3">
            <x:Addenda>
                <o:OtherData xmlns:o="http://tempuri.org/other" foo="bar"></o:OtherData>
            </x:Addenda>
            </x:Comprobante>
            XML;

        $document = new DOMDocument();
        $document->loadXML($input);

        $cleaner = new RemoveAddenda();
        $cleaner->clean($document);

        $this->assertCount(
            0,
            $document->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Addenda'),
            'Addenda element should not exists after cleaning'
        );
    }
}
