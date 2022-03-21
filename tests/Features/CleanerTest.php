<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class CleanerTest extends TestCase
{
    public function testStaticCleanStringDocument33(): void
    {
        $xmlDirty = /** @lang text */ <<<XML
            DIRTY
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi33.xsd"
            Version="3.3"/>
            XML;

        $xmlClean = Cleaner::staticClean($xmlDirty);

        $expected = /** @lang text */ <<<XML
            <?xml version="1.0"?>
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
            Version="3.3"/>
            XML;
        $this->assertXmlStringEqualsXmlString($expected, $xmlClean);
    }

    public function testCleanXmlDocument33(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi33.xsd"
            Version="3.3"/>
            XML);

        $cleaner = new Cleaner();
        $cleaner->cleanDocument($document);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
            Version="3.3"/>
            XML);
        $this->assertEquals($expected, $document);
    }

    public function testStaticCleanStringDocument40(): void
    {
        $xmlDirty = /** @lang text */ <<<XML
            DIRTY
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 cfdi40.xsd"
            Version="4.0"/>
            XML;

        $xmlClean = Cleaner::staticClean($xmlDirty);

        $expected = /** @lang text */ <<<XML
            <?xml version="1.0"?>
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
            Version="4.0"/>
            XML;
        $this->assertXmlStringEqualsXmlString($expected, $xmlClean);
    }

    public function testCleanXmlDocument40(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 cfdi44.xsd"
            Version="4.0"/>
            XML);

        $cleaner = new Cleaner();
        $cleaner->cleanDocument($document);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
            Version="4.0"/>
            XML);
        $this->assertEquals($expected, $document);
    }

    /**
     * @param string $filename
     * @testWith ["cfdi32-real.xml"]
     *           ["cfdi33-real.xml"]
     */
    public function testCleanKnownFiles(string $filename): void
    {
        $contents = $this->fileContents($filename);
        $document = $this->createDocument($contents);

        $cleaner = new Cleaner();
        $cleaner->cleanDocument($document);
        $cleanDocument = $cleaner->cleanStringToDocument($contents);

        $this->assertEquals($document, $cleanDocument);
    }
}
