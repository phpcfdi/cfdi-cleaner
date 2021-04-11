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
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveNonSatNamespacesNodes implements XmlDocumentCleanerInterface
{
    use XmlAttributeMethodsTrait;
    use XmlElementMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath = new DOMXPath($document);
        $namespaces = $this->obtainNamespacesFromDocument($xpath);
        foreach ($namespaces as $namespace) {
            if (! $this->isNamespaceAllowed($namespace)) {
                $this->removeElementsWithNamespace($xpath, $namespace);
                $this->removeAttributesWithNamespace($xpath, $namespace);
            }
        }
    }

    /** @return string[] */
    private function obtainNamespacesFromDocument(DOMXPath $xpath): array
    {
        $nodeList = $xpath->query('//namespace::*') ?: new DOMNodeList();
        return array_unique(array_column(iterator_to_array($nodeList), 'nodeValue'));
    }

    public function isNamespaceAllowed(string $namespace): bool
    {
        return str_starts_with($namespace, 'http://www.sat.gob.mx/')
            || str_starts_with($namespace, 'http://www.w3.org/');
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
