<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\SetKnownSchemaLocations;

final class SetKnownSchemaLocationsTest extends TestCase
{
    public function testSetKnownSchemaLocations(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" Version="3.3"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi.xsd"
            >
            <cfdi:Complemento>
              <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="1.1"
              xsi:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital tfd.xsd"
              />
            </cfdi:Complemento>
            </cfdi:Comprobante>
            XML
        );

        $cleaner = new SetKnownSchemaLocations();
        $cleaner->clean($document);

        $xsdCfd = 'http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
        $xsdTfd = 'http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd';
        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" Version="3.3"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 $xsdCfd"
            >
            <cfdi:Complemento>
              <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="1.1"
              xsi:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital $xsdTfd"
              />
            </cfdi:Complemento>
            </cfdi:Comprobante>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
