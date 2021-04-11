<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveUnusedNamespaces implements XmlDocumentCleanerInterface
{
    private const XMLNS = 'http://www.w3.org/XML/1998/namespace';

    public function clean(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        /**
         * It is not a \DOMNode, it is a \DOMNameSpaceNode
         * but static analysis have troubles with this undocumented of object
         * @see https://externals.io/message/104687
         *
         * @var DOMNodeList<DOMNode> $namespacesNodes
         */
        $namespacesNodes = $xpath->query('//namespace::*');

        foreach ($namespacesNodes as $namespaceNode) {
            $this->checkNamespaceNode($xpath, $namespaceNode);
        }
    }

    /**
     * @param DOMXPath $xpath
     * @param DOMNode&object $namespaceNode
     */
    private function checkNamespaceNode(DOMXPath $xpath, $namespaceNode): void
    {
        $namespace = $namespaceNode->nodeValue;
        if ($this->isNamespaceReserved($namespace)) {
            return;
        }
        if ($this->hasElementsOnNamespace($xpath, $namespace)) {
            return;
        }
        if ($this->hasAttributesOnNamespace($xpath, $namespace)) {
            return;
        }
        $this->removeNamespaceNodeAttribute($namespaceNode);
    }

    private function isNamespaceReserved(string $namespace): bool
    {
        return ('' === $namespace || self::XMLNS === $namespace);
    }

    private function hasElementsOnNamespace(DOMXPath $xpath, string $namespace): bool
    {
        $elements = $xpath->query(sprintf('//*[namespace-uri()="%1$s"]', $namespace));
        return (false !== $elements && $elements->length > 0);
    }

    private function hasAttributesOnNamespace(DOMXPath $xpath, string $namespace): bool
    {
        $attributes = $xpath->query(sprintf('//@*[namespace-uri()="%1$s"]', $namespace));
        return (false !== $attributes && $attributes->length > 0);
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
