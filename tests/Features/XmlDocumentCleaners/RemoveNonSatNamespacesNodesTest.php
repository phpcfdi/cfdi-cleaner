<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveNonSatNamespacesNodes;

final class RemoveNonSatNamespacesNodesTest extends TestCase
{
    public function testClean(): void
    {
        $document = $this->createDocument(<<< XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:x="http://tempuri.org/x" x:remove="me"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdv33.xsd"
            >
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
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:x="http://tempuri.org/x"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdv33.xsd"
            >
            <cfdi:Emisor Rfc="COSC8001137NA"/>
            <cfdi:Addenda/>
            </cfdi:Comprobante>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
