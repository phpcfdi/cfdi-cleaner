<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use DOMElement;
use DOMNode;
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

        foreach ($this->iterateNonReservedNamespaces($document) as $namespaceNode) {
            $this->cleanNameSpaceNode($rootElement, $namespaceNode);
        }
    }

    /**
     * @param DOMElement $rootElement
     * @param DOMNode&object $namespacesNode
     */
    private function cleanNameSpaceNode(DOMElement $rootElement, $namespacesNode): void
    {
        if ($rootElement === $namespacesNode->parentNode) {
            return;
        }

        $rootElement->setAttributeNS(
            XmlConstants::NAMESPACE_XMLNS,
            $namespacesNode->nodeName,
            $namespacesNode->nodeValue,
        );

        $this->removeNamespaceNodeAttribute($namespacesNode);
    }
}
