<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveNonSatNamespacesNodes;

final class RemoveNonSatNamespacesNodesTest extends TestCase
{
    public function testClean(): void
    {
        $document = $this->createDocument(<<< XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:x="url:remove:me" x:remove="me">
            <cfdi:Emisor Rfc="COSC8001137NA"/>
            <cfdi:Addenda>
              <x:remove foo="foo"/>
              <y:remove-me-too xmlns:y="lorem"/>
            </cfdi:Addenda>
            </cfdi:Comprobante> 
            XML
        );

        $cleaner = new RemoveNonSatNamespacesNodes();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<< XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:x="url:remove:me">
            <cfdi:Emisor Rfc="COSC8001137NA"/>
            <cfdi:Addenda/>
            </cfdi:Comprobante> 
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
