<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\CollapseComplemento;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveAddenda;

class CollapseComplementoTest extends TestCase
{
    public function testCleanNonCfdiNotAlterDocument(): void
    {
        $document = $this->createDocument(
            <<< XML
                <cfdi:Comprobante xmlns:cfdi="http://tempuri.org/cfd">
                  <cfdi:Complemento>
                    <foo:Foo id="first" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                  <cfdi:Complemento>
                    <foo:Foo id="second" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                </cfdi:Comprobante>
                XML
        );
        $xmlBeforeClean = $document->saveXML() ?: '';

        $cleaner = new RemoveAddenda();
        $cleaner->clean($document);

        $this->assertXmlStringEqualsXmlString($xmlBeforeClean, $document->saveXML() ?: '');
    }

    public function testCleanCfdiWithJustOneComplemento(): void
    {
        $document = $this->createDocument(
            <<< XML
                <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
                  <cfdi:Complemento>
                    <foo:Foo id="first" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                    <foo:Foo id="second" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                </cfdi:Comprobante>
                XML
        );
        $xmlBeforeClean = $document->saveXML() ?: '';

        $cleaner = new RemoveAddenda();
        $cleaner->clean($document);

        $this->assertXmlStringEqualsXmlString($xmlBeforeClean, $document->saveXML() ?: '');
    }

    public function testCleanCfdiWithThreeComplementos(): void
    {
        $document = $this->createDocument(
            <<< XML
                <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
                  <cfdi:Complemento>
                    <foo:Foo id="first" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                  <cfdi:Complemento />
                  <cfdi:Complemento>
                    <foo:Foo id="second" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                  <cfdi:Complemento />
                  <cfdi:Complemento>
                    <foo:Foo id="third" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                </cfdi:Comprobante>
                XML
        );

        $expected = $this->createDocument(
            <<< XML
                <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
                  <cfdi:Complemento>
                    <foo:Foo id="first" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                    <foo:Foo id="second" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                    <foo:Foo id="third" xmlns:foo="http://tempuri.org/foo">
                      <foo:Child/>
                    </foo:Foo>
                  </cfdi:Complemento>
                </cfdi:Comprobante>
                XML
        );

        $cleaner = new CollapseComplemento();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document);
    }
}
