<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use DOMElement;
use PhpCfdi\CfdiCleaner\Internal\XmlConstants;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class MoveNamespaceDeclarationToRoot implements XmlDocumentCleanerInterface
{
    use XmlNamespaceMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $rootElement = $document->documentElement;
        if (null === $rootElement) {
            return;
        }

        if ($this->documentHasOverlappedNamespaces($document)) {
            $this->moveNamespacesToRootOverlapped($document, $rootElement);
        } else {
            $this->moveNamespacesToRoot($document, $rootElement);
        }
    }

    private function documentHasOverlappedNamespaces(DOMDocument $document): bool
    {
        $prefixes = [];
        /** $namespaceNode is a DOMNameSpaceNode, parentNode always exists */
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            /** @var DOMElement $ownerElement */
            $ownerElement = $namespaceNode->parentNode;
            /**
             * $namespaceNode->nodeName => xmlns:cfdi
             * $namespaceNode->nodeValue => http://www.sat.gob.mx/cfd/3
             * $namespaceNode->parentNode => DOMElement where namespace definition is
             */
            $currentDefinition = [
                'namespace' => $namespaceNode->nodeValue,
                'owner' => $ownerElement,
            ];
            if (! isset($prefixes[$namespaceNode->nodeName])) {
                $prefixes[$namespaceNode->nodeName] = $currentDefinition;
                continue;
            }
            if ($ownerElement->hasAttribute($namespaceNode->nodeName)
                && $prefixes[$namespaceNode->nodeName] !== $currentDefinition) {
                return true;
            }
        }
        return false;
    }

    private function moveNamespacesToRootOverlapped(DOMDocument $document, DOMElement $rootElement): void
    {
        $namespaces = [];
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if ($rootElement === $namespaceNode->parentNode) {
                continue; // already on root
            }
            $nsPrefix = (string) $namespaceNode->nodeName;
            $nsLocation = (string) $namespaceNode->nodeValue;
            $namespaces[$nsPrefix] = $namespaces[$nsPrefix] ?? $nsLocation;
            // do not iterate on overlapped
            if ($namespaces[$nsPrefix] !== $nsLocation) {
                continue;
            }
            // soft-write the xml namespace declaration if it does not exist yet
            if (! $rootElement->hasAttribute($nsPrefix)) {
                $rootElement->setAttribute($nsPrefix, $nsLocation);
            }
        }
        // ditry hack to remove child namespace declaration
        $document->loadXML($document->saveXML() ?: '', LIBXML_NSCLEAN | LIBXML_PARSEHUGE);
    }

    private function moveNamespacesToRoot(DOMDocument $document, DOMElement $rootElement): void
    {
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if ($rootElement === $namespaceNode->parentNode) {
                continue;
            }

            $nsNodeName = $namespaceNode->nodeName;
            $nsNodeValue = $namespaceNode->nodeValue;
            if ($nsNodeValue && ! $rootElement->hasAttribute($nsNodeName)) {
                $rootElement->setAttributeNS(XmlConstants::NAMESPACE_XMLNS, $nsNodeName, $nsNodeValue);
            }

            $this->removeNamespaceNodeAttribute($namespaceNode);
        }
    }
}
