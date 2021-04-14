<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use PhpCfdi\CfdiCleaner\Internal\XmlAttributeMethodsTrait;
use PhpCfdi\CfdiCleaner\Internal\XmlElementMethodsTrait;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveNonSatNamespacesNodes implements XmlDocumentCleanerInterface
{
    use XmlAttributeMethodsTrait;
    use XmlElementMethodsTrait;
    use XmlNamespaceMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        $namespaces = $this->obtainNamespacesFromDocument($document);
        foreach ($namespaces as $namespace) {
            if (! $this->isNamespaceRelatedToSat($namespace)) {
                $this->removeElementsWithNamespace($xpath, $namespace);
                $this->removeAttributesWithNamespace($xpath, $namespace);
            }
        }
    }

    /** @return string[] */
    private function obtainNamespacesFromDocument(DOMDocument $document): array
    {
        $namespaces = [];
        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            $namespaces[] = $namespaceNode->nodeValue;
        }
        return array_unique($namespaces);
    }

    private function removeElementsWithNamespace(DOMXPath $xpath, string $namespace): void
    {
        /** @var DOMNodeList<DOMElement> $elements */
        $elements = $xpath->query(sprintf('//*[namespace-uri()="%1$s"]', $namespace));
        foreach ($elements as $element) {
            $this->elementRemove($element);
        }
    }

    private function removeAttributesWithNamespace(DOMXPath $xpath, string $namespace): void
    {
        /** @var DOMNodeList<DOMAttr> $attributes */
        $attributes = $xpath->query(sprintf('//@*[namespace-uri()="%1$s"]', $namespace));
        foreach ($attributes as $attribute) {
            $this->attributeRemove($attribute);
        }
    }
}
