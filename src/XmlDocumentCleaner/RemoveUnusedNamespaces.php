<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

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
        if ($this->hasElementsOnNamespace($xpath, $namespace)) {
            return;
        }
        if ($this->hasAttributesOnNamespace($xpath, $namespace)) {
            return;
        }
        $this->removeNamespaceNodeAttribute($namespaceNode);
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
}
