<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\MoveNamespaceDeclarationToRoot;

final class MoveNamespaceDeclarationToRootTest extends TestCase
{
    public function testMoveNamespaceDeclarationToRoot(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root">
              <foo:foo xmlns:foo="http://tempuri.org/foo"/>
              <bar:bar xmlns:bar="http://tempuri.org/bar"/>
              <xee/>
            </r:root>
            XML
        );

        $cleaner = new MoveNamespaceDeclarationToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root"
              xmlns:foo="http://tempuri.org/foo" xmlns:bar="http://tempuri.org/bar">
              <foo:foo/>
              <bar:bar/>
              <xee/>
            </r:root>
            XML
        );
        $this->assertEquals($expected, $document);
    }

    public function testMoveNamespaceDeclarationToRootWithOverlappedNamespaces(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
            <cfdi:Complemento>
              <cfdi:Otro xmlns:cfdi="http://www.sat.gob.mx/otro" />
              <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" />
            </cfdi:Complemento>
            </cfdi:Comprobante>
            XML
        );

        $cleaner = new MoveNamespaceDeclarationToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital">
            <cfdi:Complemento>
              <cfdi:Otro xmlns:cfdi="http://www.sat.gob.mx/otro" />
              <tfd:TimbreFiscalDigital />
            </cfdi:Complemento>
            </cfdi:Comprobante>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
