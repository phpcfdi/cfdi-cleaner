<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Internal;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Generator;

/**
 * @internal
 */
trait XmlNamespaceMethodsTrait
{
    /**
     * @param DOMDocument $document
     * @return Generator&iterable<DOMNode>
     */
    private function iterateNonReservedNamespaces(DOMDocument $document): Generator
    {
        $xpath = new DOMXPath($document);
        $namespaceNodes = $xpath->query('//namespace::*') ?: new DOMNodeList();
        foreach ($namespaceNodes as $namespaceNode) {
            if (! $this->isNamespaceReserved($namespaceNode->nodeValue)) {
                yield $namespaceNode;
            }
        }
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

    private function isNamespaceReserved(string $namespace): bool
    {
        $reservedNameSpaces = [
            '',                             // empty
            XmlConstants::NAMESPACE_XML,    // xml
            XmlConstants::NAMESPACE_XMLNS,  // xml namespace allocation
            XmlConstants::NAMESPACE_XSI,    // xml schema instance
        ];
        return (in_array($namespace, $reservedNameSpaces, true));
    }

    private function isNamespaceRelatedToSat(string $namespace): bool
    {
        return str_starts_with($namespace, 'http://www.sat.gob.mx/');
    }
}
