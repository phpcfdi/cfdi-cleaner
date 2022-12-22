<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Cleaner;
use PhpCfdi\CfdiCleaner\ExcludeList;
use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\MoveNamespaceDeclarationToRoot;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveAddenda;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveNonSatNamespacesNodes;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveNonSatSchemaLocations;

final class CleanerExcludeTest extends TestCase
{
    public function testCleanerExcludeAddenda(): void
    {
        $xml = /** @lang text */ <<<XML
            <?xml version="1.0"?>
            <cfdi:Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:cfdi="http://www.sat.gob.mx/cfd/4"
            xsi:schemaLocation="http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd"
            Version="4.0">
            <cfdi:Addenda>
                <foo:Main xmlns:foo="urn:foo" id="1" />
            </cfdi:Addenda>
            </cfdi:Comprobante>
            XML;

        $excludeList = new ExcludeList(
            RemoveAddenda::class,
            RemoveNonSatNamespacesNodes::class,
            RemoveNonSatSchemaLocations::class,
            MoveNamespaceDeclarationToRoot::class
        );

        $cleaner = new Cleaner();
        $cleaner->exclude($excludeList);

        $xmlClean = $cleaner->cleanStringToString($xml);

        $this->assertXmlStringEqualsXmlString($xml, $xmlClean);
    }
}
