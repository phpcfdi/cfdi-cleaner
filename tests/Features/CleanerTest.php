<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class CleanerTest extends TestCase
{
    public function testStaticCleanStringDocument(): void
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

    public function testCleanXmlDocument(): void
    {
        $document = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 cfdi33.xsd"
            Version="3.3"/>        
            XML
        );

        $cleaner = new Cleaner();
        $cleaner->cleanDocument($document);

        $expected = $this->createDocument(<<<XML
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd"
            Version="3.3"/>        
            XML
        );
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
