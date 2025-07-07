<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use DOMElement;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

final class RenameElementAddPrefix implements XmlDocumentCleanerInterface
{
    use XmlNamespaceMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $rootElement = $document->documentElement;
        if (null === $rootElement) {
            return;
        }

        $this->cleanElement($rootElement);

        // remove unused xmlns declarations
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if ('xmlns' === $namespaceNode->nodeName) {
                /** @var DOMElement $parentNode */
                $parentNode = $namespaceNode->parentNode;
                if ('' !== $this->queryPrefix($parentNode)) {
                    $this->removeNamespaceNodeAttribute($namespaceNode);
                }
            }
        }

        // Remove redundant namespace declarations
        // We are using saveXML and loadXML because normalizeDocument method doesn't seem to reset the namespaces;
        $document->loadXML((string) $document->saveXML(), LIBXML_NSCLEAN | LIBXML_PARSEHUGE);
    }

    private function cleanElement(DOMElement $element): void
    {
        $this->cleanElementPrefix($element);

        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $this->cleanElement($child);
            }
        }
    }

    private function cleanElementPrefix(DOMElement $element): void
    {
        $elementPrefix = (string) $element->prefix;
        if ('' !== $elementPrefix) {
            return; // do nothing if element has a prefix
        }

        $targetPrefix = $this->queryPrefix($element);
        if ('' === $targetPrefix) {
            return; // do nothing if there is no registered prefix
        }

        // set the correct prefix
        $element->prefix = $targetPrefix;
    }

    private function queryPrefix(DOMElement $element): string
    {
        $namespace = (string) $element->namespaceURI;
        if ('' === $namespace) {
            return '';
        }

        /** @var DOMDocument $document */
        $document = $element->ownerDocument;

        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            if ($element !== $namespaceNode->parentNode) {
                continue;
            }

            $prefix = (string) $namespaceNode->prefix;
            if ('' !== $prefix && $namespaceNode->nodeValue === $namespace) {
                return $prefix;
            }
        }

        return '';
    }
}
