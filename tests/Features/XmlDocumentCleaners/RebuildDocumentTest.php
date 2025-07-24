<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\Tests\Traits\UseSatNsRegistryTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RebuildDocument;

final class RebuildDocumentTest extends TestCase
{
    use UseSatNsRegistryTrait;

    public function testRebuildDocument(): void
    {
        $document = $this->createDocument(<<<XML
            <x:Comprobante
              xmlns:i="http://www.w3.org/2001/XMLSchema-instance"
              xmlns:x="http://www.sat.gob.mx/cfd/4"
              xmlns:n="http://www.sat.gob.mx/nomina"
              attr="foo-comprobante"
              i:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
              >
            <x:Complemento>
                <!-- many more data -->
                <t:TimbreFiscalDigital xmlns:t="http://www.sat.gob.mx/TimbreFiscalDigital" attr="foo-tfd" />
                <n:Nomina attr="foo-nomina">sample</n:Nomina>
            </x:Complemento>
            <x:Addenda>
              <o:MyAddenda xmlns:o="http://tempuri.org/foo"><![CDATA[ 1" <> 3*(1/3)" ]]></o:MyAddenda>
            </x:Addenda>
            </x:Comprobante>
            XML);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante
              xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
              xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              attr="foo-comprobante"
              xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
              >
            <cfdi:Complemento>
                <!-- many more data -->
                <tfd:TimbreFiscalDigital xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" attr="foo-tfd" />
                <nomina:Nomina xmlns:nomina="http://www.sat.gob.mx/nomina" attr="foo-nomina">sample</nomina:Nomina>
            </cfdi:Complemento>
            <cfdi:Addenda>
              <o:MyAddenda xmlns:o="http://tempuri.org/foo"><![CDATA[ 1" <> 3*(1/3)" ]]></o:MyAddenda>
            </cfdi:Addenda>
            </cfdi:Comprobante>
            XML);

        $cleaner = new RebuildDocument();
        $cleaner->clean($document);

        $this->assertSame($expected->saveXML(), $document->saveXML());
    }

    public function testKnowAllNamespacesFromSatNsRegistry(): void
    {
        $registry = $this->getSatNsRegistry();

        // re-create the known list of namespace#version => xsd-location
        $expected = [
            'http://www.w3.org/2001/XMLSchema-instance' => 'xsi',
        ];
        foreach ($registry as $entry) {
            $namespace = $entry->namespace ?? '';
            $prefix = $entry->prefix ?? '';
            if ($namespace && $prefix) {
                if (! isset($expected[$namespace])) {
                    $expected[$namespace] = $prefix;
                    continue;
                }
                if ($expected[$namespace] !== $prefix) {
                    $this->fail(sprintf(
                        'The namespace %s has a prefix %s that does not match with %s',
                        $namespace,
                        $expected[$namespace],
                        $prefix
                    ));
                }
            }
        }
        ksort($expected);

        $knownLocations = RebuildDocument::getKnownNamespacePrefixEntries();
        ksort($knownLocations);

        $this->assertSame(
            $expected,
            $knownLocations,
            'The list of known namespace => prefix is different from phpcfdi/sat-ns-registry',
        );
    }
}
