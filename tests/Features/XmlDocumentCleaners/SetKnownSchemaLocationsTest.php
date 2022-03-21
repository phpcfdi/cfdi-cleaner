<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\SetKnownSchemaLocations;

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
            XML);

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
            XML);
        $this->assertEquals($expected, $document);
    }

    public function testSetKnownSchemaLocationsWithoutVersion(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi.xsd"/>
            XML);

        $cleaner = new SetKnownSchemaLocations();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi.xsd"/>
            XML);
        $this->assertEquals($expected, $document);
    }

    public function testSetKnownSchemaLocationsWithUnknownNamespace(): void
    {
        $document = $this->createDocument(<<<XML
            <foo:Foo xmlns:foo="http://tempuri.org/foo" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://tempuri.org/foo foo.xsd" />
            XML);

        $cleaner = new SetKnownSchemaLocations();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <foo:Foo xmlns:foo="http://tempuri.org/foo" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://tempuri.org/foo foo.xsd" />
            XML);
        $this->assertEquals($expected, $document);
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testKnowAllLocationsFromSatNsRegistry(): void
    {
        // obtain the list of known locations from phpcfdi/sat-ns-registry
        $satNsRegistryUrl = 'https://raw.githubusercontent.com/phpcfdi/sat-ns-registry/master/complementos_v1.json';
        /** @var array<array{namespace: ?string, version: ?string, xsd: ?string}> $registry */
        $registry = json_decode(file_get_contents($satNsRegistryUrl) ?: '[]', true, 512, JSON_THROW_ON_ERROR);

        // re-create the known list of namespace#version => xsd-location
        $expected = [];
        foreach ($registry as $entry) {
            $namespace = $entry['namespace'] ?? '';
            $version = $entry['version'] ?? '';
            $xsd = $entry['xsd'] ?? '';
            if ($namespace && $xsd) {
                $expected[$namespace . '#' . $version] = $xsd;
            }
        }
        asort($expected);

        $knownLocations = SetKnownSchemaLocations::getKnownNamespaces();
        asort($knownLocations);

        $this->assertSame(
            $expected,
            $knownLocations,
            'The list of known namespace#version => xsd-location is different from phpcfdi/sat-ns-registry',
        );
    }
}
