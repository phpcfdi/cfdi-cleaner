<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Unit\Internal;

use DOMDocument;
use DOMElement;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\Tests\TestCase;

final class XmlNamespaceMethodsTraitTest extends TestCase
{
    public function testIterateOnRemovedNamespaces(): void
    {
        $specimen = new class () {
            use XmlNamespaceMethodsTrait;

            /**
             * @param DOMDocument $document
             * @return array<string, string>
             */
            public function obtainNamespaces(DOMDocument $document): array
            {
                $namespaces = [];
                foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
                    $namespaces[$namespaceNode->prefix] = $namespaceNode->nodeValue;
                }
                asort($namespaces);
                return $namespaces;
            }

            /**
             * @param DOMDocument $document
             * @param string $namespace
             */
            public function removeNamespaceNodesWithNamespace(DOMDocument $document, string $namespace): void
            {
                foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
                    if ($namespace === $namespaceNode->nodeValue) {
                        $this->removeNamespaceNodeAttribute($namespaceNode);
                    }
                }
            }
        };

        $namespaces = [
            'root' => 'http://tempuri.org/root',
            'unused' => 'http://tempuri.org/unused',
            'foo' => 'http://tempuri.org/foo',
        ];
        asort($namespaces);

        $document = $this->createDocument(<<<XML
            <root:root xmlns:root="http://tempuri.org/root" xmlns:unused="http://tempuri.org/unused">
              <foo:foo xmlns:foo="http://tempuri.org/foo"/>
            </root:root>
            XML);

        $this->assertSame($namespaces, $specimen->obtainNamespaces($document));

        // remove unused namespace
        $specimen->removeNamespaceNodesWithNamespace($document, 'http://tempuri.org/unused');
        unset($namespaces['unused']);

        // list of nodes should be the same
        $this->assertSame($namespaces, $specimen->obtainNamespaces($document));

        $expected = $this->createDocument(<<<XML
            <root:root xmlns:root="http://tempuri.org/root">
              <foo:foo xmlns:foo="http://tempuri.org/foo"/>
            </root:root>
            XML);
        $this->assertEquals($expected, $document);
    }

    /** @return array<string, array{string, string}> */
    public function providerRemoveNamespaceNodeAttribute(): array
    {
        return [
            'unused' => [
                'xmlns:unused',
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test xmlns:unused="http://tempuri.org/root"/>
                    </r:root>
                    XML,
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test/>
                    </r:root>
                    XML,
            ],
            'no prefix' => [
                'xmlns',
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test xmlns="http://tempuri.org/root"/>
                    </r:root>
                    XML,
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test/>
                    </r:root>
                    XML,
            ],
            'no prefix no content' => [
                'xmlns',
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test xmlns=""/>
                    </r:root>
                    XML,
                <<<XML
                    <r:root xmlns:r="http://tempuri.org/root">
                      <r:test/>
                    </r:root>
                    XML,
            ],
        ];
    }

    /** @dataProvider providerRemoveNamespaceNodeAttribute */
    public function testRemoveNamespaceNodeAttribute(string $target, string $xmlInput, string $xmlExpected): void
    {
        $specimen = new class () {
            use XmlNamespaceMethodsTrait {
                iterateNonReservedNamespaces as public;
                removeNamespaceNodeAttribute as public;
            }
        };

        $document = $this->createDocument($xmlInput);

        /** @var DOMElement $testElement */
        $testElement = $document->getElementsByTagName('test')->item(0);

        // find and remove unused "xmlns:unsused"
        foreach ($specimen->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if ($testElement === $namespaceNode->parentNode && $target === $namespaceNode->nodeName) {
                $specimen->removeNamespaceNodeAttribute($namespaceNode);
            }
        }

        $expected = $this->createDocument($xmlExpected);

        $this->assertEquals($expected, $document);
        $this->assertSame($expected->saveXML(), $document->saveXML(), 'Expected XML is not identical');
    }
}
