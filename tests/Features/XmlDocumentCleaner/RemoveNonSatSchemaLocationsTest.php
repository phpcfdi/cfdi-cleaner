<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveNonSatSchemaLocations;

final class RemoveNonSatSchemaLocationsTest extends TestCase
{
    public function testClean(): void
    {
        $document = $this->createDocument(<<< XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xmlns:extra="http://www.sat.gob.mx/extra"
            xsi:schemaLocation="
             http://www.sat.gob.mx/cfd/3        cfd33.xsd
             http://www.sat.gob.mx/extra        extra.xsd
             http://tempuri.org/bar             bar.xsd
            ">
            <cfdi:Complemento>
              <extra:Extra/>
            </cfdi:Complemento>
            <cfdi:Addenda>
              <foo:foo xmlns:foo="http://tempuri.org/foo" xsi:schemaLocation="http://tempuri.org/foo foo.xsd"/>
            </cfdi:Addenda>
            </cfdi:Comprobante> 
            XML
        );

        $cleaner = new RemoveNonSatSchemaLocations();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<< XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xmlns:extra="http://www.sat.gob.mx/extra"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfd33.xsd http://www.sat.gob.mx/extra extra.xsd">
            <cfdi:Complemento>
              <extra:Extra/>
            </cfdi:Complemento>
            <cfdi:Addenda>
              <foo:foo xmlns:foo="http://tempuri.org/foo"/>
            </cfdi:Addenda>
            </cfdi:Comprobante> 
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
