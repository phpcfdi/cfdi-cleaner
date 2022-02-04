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
     * @phpstan-impure
     * @internal The actual returned class is Generator<DOMNameSpaceNode> (undocumented)
     */
    private function iterateNonReservedNamespaces(DOMDocument $document): Generator
    {
        $xpath = new DOMXPath($document);
        $namespaceNodes = $xpath->query('//namespace::*') ?: new DOMNodeList();
        foreach ($namespaceNodes as $namespaceNode) {
            // discard removed namespaces they could be returned by XPath
            if (null === $namespaceNode->namespaceURI) {
                continue;
            }

            // discard reserved (internal xml) namespaces
            if ($this->isNamespaceReserved($namespaceNode->namespaceURI)) {
                continue;
            }

            yield $namespaceNode;
        }
    }

    /**
     * @param DOMNode&object $namespaceNode
     */
    private function removeNamespaceNodeAttribute($namespaceNode): void
    {
        $ownerElement = $namespaceNode->parentNode;
        if ($ownerElement instanceof DOMElement) {
            $localName = ('xmlns' === $namespaceNode->localName) ? '' : $namespaceNode->localName;
            $ownerElement->removeAttributeNS((string) $namespaceNode->nodeValue, $localName);
        }
    }

    private function isNamespaceReserved(string $namespace): bool
    {
        $reservedNameSpaces = [
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
