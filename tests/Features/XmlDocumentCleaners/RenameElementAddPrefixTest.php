<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RenameElementAddPrefix;

final class RenameElementAddPrefixTest extends TestCase
{
    public function testRenameElementAddPrefix(): void
    {
        // NOTICE:
        // - no prefix definition *before* prefixed definition on root
        // - first element is not prefixed
        // - second element is prefixed but contains superfluous declaration
        // - third element is prefixed but contains unused declaration
        $document = $this->createDocument(<<<XML
            <r:root xmlns="http://tempuri.org/root" xmlns:r="http://tempuri.org/root" id="0">
              <first xmlns="http://tempuri.org/root" id="1" />
              <r:second xmlns:r="http://tempuri.org/root" id="2" />
              <r:third xmlns="http://tempuri.org/root" id="3" />
            </r:root>
            XML);

        $cleaner = new RenameElementAddPrefix();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root" id="0">
              <r:first id="1" />
              <r:second id="2" />
              <r:third id="3" />
            </r:root>
            XML);
        $this->assertEquals($expected->saveXML(), $document->saveXML());
    }

    public function testRemoveDuplicatedNamespaceAsDefault(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root" xmlns="http://www.sat.gob.mx/cfd/3"/>
            XML);

        $cleaner = new RenameElementAddPrefix();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root"/>
            XML);
        $this->assertEquals($expected->saveXML(), $document->saveXML());
    }

    public function testRemoveEmptyNamespaceWithoutPrefix(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root" xmlns=""/>
            XML);

        $cleaner = new RenameElementAddPrefix();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root"/>
            XML);
        $this->assertEquals($expected->saveXML(), $document->saveXML());
    }
}
