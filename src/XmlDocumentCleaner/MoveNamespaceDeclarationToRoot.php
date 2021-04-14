<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class MoveNamespaceDeclarationToRoot implements XmlDocumentCleanerInterface
{
    private const NAMESPACE_XML = 'http://www.w3.org/XML/1998/namespace';

    private const NAMESPACE_XMLNS = 'http://www.w3.org/2000/xmlns/';

    public function clean(DOMDocument $document): void
    {
        $rootElement = $document->documentElement;
        if (null === $rootElement) {
            return;
        }

        $xpath = new DOMXPath($document);
        $namespacesNodes = $xpath->query('//namespace::*') ?: new DOMNodeList();

        foreach ($namespacesNodes as $namespacesNode) {
            $this->cleanNameSpaceNode($rootElement, $namespacesNode);
        }
    }

    /**
     * @param DOMElement $rootElement
     * @param DOMNode&object $namespacesNode
     */
    private function cleanNameSpaceNode(DOMElement $rootElement, $namespacesNode): void
    {
        $namespace = $namespacesNode->nodeValue;

        if ($this->isNamespaceReserved($namespace)) {
            return;
        }

        if ($rootElement === $namespacesNode->parentNode) {
            return;
        }

        $rootElement->setAttributeNS(self::NAMESPACE_XMLNS, $namespacesNode->nodeName, $namespace);
        $this->removeNamespaceNodeAttribute($namespacesNode);
    }

    private function isNamespaceReserved(string $namespace): bool
    {
        return ('' === $namespace || self::NAMESPACE_XML === $namespace);
    }

    /**
     * @param DOMNode&object $namespaceNode
     */
    private function removeNamespaceNodeAttribute($namespaceNode): void
    {
        $ownerElement = $namespaceNode->parentNode;
        if ($ownerElement instanceof DOMElement) {
            $ownerElement->removeAttributeNS($namespaceNode->nodeValue, $namespaceNode->localName);
        }
    }
}
