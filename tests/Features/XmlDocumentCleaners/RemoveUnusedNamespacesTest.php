<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveUnusedNamespaces;

final class RemoveUnusedNamespacesTest extends TestCase
{
    public function testRemoveUnusedNamespacesOnRoot(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root
              xmlns:b="http://tempuri.org/bar"
              xmlns:r="http://tempuri.org/root"
              xmlns:f="http://tempuri.org/foo"
            />
            XML
        );

        $cleaner = new RemoveUnusedNamespaces();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root"/>
            XML
        );
        $this->assertEquals($expected, $document);
    }

    public function testRemoveUnusedNamespacesOnChildren(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:b="http://tempuri.org/bar" xmlns:r="http://tempuri.org/root" xmlns:f="http://tempuri.org/foo">
              <a:child xmlns:a="http://tempuri.org/a">
                <a:child xmlns:xee="http://tempuri.org/xee" f:foo="foo"/>
              </a:child>
            </r:root>
            XML
        );

        $cleaner = new RemoveUnusedNamespaces();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri.org/root" xmlns:f="http://tempuri.org/foo">
              <a:child xmlns:a="http://tempuri.org/a">
                <a:child f:foo="foo"/>
              </a:child>
            </r:root>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
