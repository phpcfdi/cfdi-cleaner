<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use DOMNode;
use DOMXPath;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveUnusedNamespaces implements XmlDocumentCleanerInterface
{
    use XmlNamespaceMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
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
        $prefix = ('' !== strval($namespaceNode->prefix)) ? $namespaceNode->prefix . ':' : '';

        if (! $this->isPrefixedNamespaceOnUse($xpath, $namespace, $prefix)) {
            $this->removeNamespaceNodeAttribute($namespaceNode);
        }
    }

    private function isPrefixedNamespaceOnUse(DOMXPath $xpath, string $namespace, string $prefix): bool
    {
        if ($this->hasElementsOnNamespace($xpath, $namespace, $prefix)) {
            return true;
        }
        if ($this->hasAttributesOnNamespace($xpath, $namespace, $prefix)) {
            return true;
        }
        return false;
    }

    private function hasElementsOnNamespace(DOMXPath $xpath, string $namespace, string $prefix): bool
    {
        $elements = $xpath->query(
            sprintf('//*[namespace-uri()="%1$s" and name()=concat("%2$s", local-name())]', $namespace, $prefix),
        );
        return (false !== $elements && $elements->length > 0);
    }

    private function hasAttributesOnNamespace(DOMXPath $xpath, string $namespace, string $prefix): bool
    {
        $elements = $xpath->query(
            sprintf('//@*[namespace-uri()="%1$s" and name()=concat("%2$s", local-name())]', $namespace, $prefix),
        );
        return (false !== $elements && $elements->length > 0);
    }
}
