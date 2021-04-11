<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveUnusedNamespaces;

final class RemoveUnusedNamespacesTest extends TestCase
{
    public function testRemoveUnusedNamespacesOnRoot(): void
    {
        $document = $this->createDocument(
            '<r:root xmlns:b="http://tempuri/bar" xmlns:r="http://tempuri/root" xmlns:f="http://tempuri/foo" />'
        );

        $cleaner = new RemoveUnusedNamespaces();
        $cleaner->clean($document);

        $expected = $this->createDocument(
            '<r:root xmlns:r="http://tempuri/root" />'
        );
        $this->assertEquals($expected, $document);
    }

    public function testRemoveUnusedNamespacesOnChildren(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:b="http://tempuri/bar" xmlns:r="http://tempuri/root" xmlns:f="http://tempuri/foo">
              <a:child xmlns:a="http://tempuri/a">
                <a:child xmlns:xee="http://tempuri/xee" f:foo="foo"/>
              </a:child>
            </r:root>
            XML
        );

        $cleaner = new RemoveUnusedNamespaces();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="http://tempuri/root" xmlns:f="http://tempuri/foo">
              <a:child xmlns:a="http://tempuri/a">
                <a:child f:foo="foo"/>
              </a:child>
            </r:root>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
