<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Document;
use PhpCfdi\CfdiCleaner\RemoveDuplicatedCfdi3Namespace;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

class RemoveDuplicatedCfdi3NamespaceTest extends TestCase
{
    public function testCleaning(): void
    {
        $input = '<cfdi:Comprobante  xmlns="http://www.sat.gob.mx/cfd/3"  xmlns:cfdi="http://www.sat.gob.mx/cfd/3"/>';
        $expected = '<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3"/>';

        $document = Document::load($input);

        $cleaner = new RemoveDuplicatedCfdi3Namespace();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document->getXmlContents());
    }
}
