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

    /** @var DOMXPath */
    private $xpath;

    /** @var array<string, bool> */
    private $prefixedNamespaceOnUseCache;

    private function setUp(DOMXPath $xpath): void
    {
        $this->xpath = $xpath;
        $this->prefixedNamespaceOnUseCache = [];
    }

    public function clean(DOMDocument $document): void
    {
        $this->setUp(new DOMXPath($document));

        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            $this->checkNamespaceNode($namespaceNode);
        }
    }

    /**
     * @param DOMNode&object $namespaceNode
     */
    private function checkNamespaceNode($namespaceNode): void
    {
        $namespace = $namespaceNode->nodeValue;
        if (null === $namespace) {
            return;
        }
        $prefix = ('' !== strval($namespaceNode->prefix)) ? $namespaceNode->prefix . ':' : '';

        if (! $this->isPrefixedNamespaceOnUseCached($namespace, $prefix)) {
            $this->removeNamespaceNodeAttribute($namespaceNode);
        }
    }

    /**
     * Function `isPrefixedNamespaceOnUse` is costly, use cache to avoid repetetive calls.
     * @see isPrefixedNamespaceOnUse
     */
    private function isPrefixedNamespaceOnUseCached(string $namespace, string $prefix): bool
    {
        $key = sprintf('namespace=%s;prefix=%s', $namespace, $prefix);
        if (! array_key_exists($key, $this->prefixedNamespaceOnUseCache)) {
            $this->prefixedNamespaceOnUseCache[$key] = $this->isPrefixedNamespaceOnUse($namespace, $prefix);
        }
        return $this->prefixedNamespaceOnUseCache[$key];
    }

    private function isPrefixedNamespaceOnUse(string $namespace, string $prefix): bool
    {
        if ($this->hasElementsOnNamespace($namespace, $prefix)) {
            return true;
        }
        if ($this->hasAttributesOnNamespace($namespace, $prefix)) {
            return true;
        }
        return false;
    }

    private function hasElementsOnNamespace(string $namespace, string $prefix): bool
    {
        $elements = $this->xpath->query(
            sprintf('(//*[namespace-uri()="%1$s" and name()=concat("%2$s", local-name())])[1]', $namespace, $prefix),
        );
        return (false !== $elements && $elements->length > 0);
    }

    private function hasAttributesOnNamespace(string $namespace, string $prefix): bool
    {
        $elements = $this->xpath->query(
            sprintf('(//@*[namespace-uri()="%1$s" and name()=concat("%2$s", local-name())])[1]', $namespace, $prefix),
        );
        return (false !== $elements && $elements->length > 0);
    }
}
