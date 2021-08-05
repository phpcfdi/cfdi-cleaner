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
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if (! isset($prefixes[$namespaceNode->nodeName])) {
                $prefixes[$namespaceNode->nodeName] = $namespaceNode->nodeValue;
                continue;
            }
            if ($prefixes[$namespaceNode->nodeName] !== $namespaceNode->nodeValue) {
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
            $nsPrefix = $namespaceNode->nodeName;
            $nsLocation = $namespaceNode->nodeValue;
            $namespaces[$nsPrefix] = $namespaces[$nsPrefix] ?? $nsLocation;
            // do not iterate on overlapped
            if ($namespaces[$nsPrefix] !== $nsLocation) {
                continue;
            }
            // soft-write the xml namespace declaration if it does not exist yet
            if (! $rootElement->hasAttribute($namespaceNode->nodeName)) {
                $rootElement->setAttribute($namespaceNode->nodeName, $namespaceNode->nodeValue);
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

            if (! $rootElement->hasAttribute($namespaceNode->nodeName)) {
                $rootElement->setAttributeNS(
                    XmlConstants::NAMESPACE_XMLNS,
                    $namespaceNode->nodeName,
                    $namespaceNode->nodeValue,
                );
            }

            $this->removeNamespaceNodeAttribute($namespaceNode);
        }
    }
}
