<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Unit\Internal;

use PhpCfdi\CfdiCleaner\Internal\CfdiXPath;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class CfdiXPathTest extends TestCase
{
    /** @return array<string, array{string}> */
    public function providerCreateCfdiVersions(): array
    {
        return [
            'CFDI 3.3' => [<<<XML
                <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" Version="3.3"
                  xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
                  x:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
                >
                <cfdi:Complemento>
                  <leyendasFisc:LeyendasFiscales xmlns:leyendasFisc="http://www.sat.gob.mx/leyendasFiscales"
                   Version="1.0" x:schemaLocation="http://www.sat.gob.mx/leyendasFiscales leyendasFisc.xsd"
                  >
                    <leyendasFisc:Leyenda disposicionFiscal="RESDERAUTH" norma="Art 2. Fracc. IV." textoLeyenda="..." />
                  </leyendasFisc:LeyendasFiscales>
                </cfdi:Complemento>
                <cfdi:Complemento>
                  <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="1.1"
                    x:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital TimbreFiscalDigitalv11.xsd"
                    UUID="AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE" NoCertificadoSAT="00001000000504465028"
                    FechaTimbrado="2022-01-12T12:39:34" RfcProvCertif="SAT970701NN3"
                    SelloCFD="...5tSZhA==" SelloSAT="...aobTwQ=="/>
                </cfdi:Complemento>
                </cfdi:Comprobante>
                XML],
            'CFDI 4.0' => [<<<XML
                <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" Version="4.0"
                  xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
                  x:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
                >
                <cfdi:Complemento>
                  <leyendasFisc:LeyendasFiscales xmlns:leyendasFisc="http://www.sat.gob.mx/leyendasFiscales"
                   Version="1.0" x:schemaLocation="http://www.sat.gob.mx/leyendasFiscales leyendasFisc.xsd"
                  >
                    <leyendasFisc:Leyenda disposicionFiscal="RESDERAUTH" norma="Art 2. Fracc. IV." textoLeyenda="..." />
                  </leyendasFisc:LeyendasFiscales>
                </cfdi:Complemento>
                <cfdi:Complemento>
                  <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="1.1"
                    x:schemaLocation="http://www.sat.gob.mx/TimbreFiscalDigital TimbreFiscalDigitalv11.xsd"
                    UUID="AAAAAAAA-BBBB-CCCC-DDDD-EEEEEEEEEEEE" NoCertificadoSAT="00001000000504465028"
                    FechaTimbrado="2022-01-12T12:39:34" RfcProvCertif="SAT970701NN3"
                    SelloCFD="...5tSZhA==" SelloSAT="...aobTwQ=="/>
                </cfdi:Complemento>
                </cfdi:Comprobante>
                XML],
        ];
    }

    /** @dataProvider providerCreateCfdiVersions */
    public function testCreateCfdiVersions(string $source): void
    {
        $document = $this->createDocument($source);
        $xpath = CfdiXPath::createFromDocument($document);

        $attributes = [];
        foreach ($xpath->queryAttributes('//cfdi:Complemento//@Version') as $attribute) {
            $attributes[] = $attribute->nodeValue;
        }
        $this->assertSame(['1.0', '1.1'], $attributes);

        $elements = [];
        foreach ($xpath->queryElements('/cfdi:Comprobante/cfdi:Complemento/*') as $element) {
            $elements[] = $element->nodeName;
        }
        $this->assertSame(['leyendasFisc:LeyendasFiscales', 'tfd:TimbreFiscalDigital'], $elements);

        $this->assertCount(3, iterator_to_array($xpath->querySchemaLocations()));

        $this->assertSame([], iterator_to_array($xpath->queryAttributes('//@FOOBAR')));
        $this->assertSame([], iterator_to_array($xpath->queryElements('//FOOBAR')));
    }

    public function testNonAllowedNamespace(): void
    {
        $document = $this->createDocument(
            <<<XML
                <cfdi:Comprobante xmlns:cfdi="http://tempuri.org/cfdi"/>
                XML
        );
        $xpath = CfdiXPath::createFromDocument($document);
        $this->assertCount(0, $xpath->queryElements('/cfdi:Comprobante'));
    }

    public function testAllowedNamespaceWithDifferentPrefix(): void
    {
        $document = $this->createDocument(
            <<<XML
                <factura:Comprobante xmlns:factura="http://www.sat.gob.mx/cfd/4"/>
                XML
        );
        $xpath = CfdiXPath::createFromDocument($document);
        $this->assertCount(1, $xpath->queryElements('/cfdi:Comprobante'));
    }
}
