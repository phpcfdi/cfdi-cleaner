<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\Internal\SchemaLocation;
use PhpCfdi\CfdiCleaner\Internal\XmlAttributeMethodsTrait;
use PhpCfdi\CfdiCleaner\Internal\XmlConstants;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class MoveSchemaLocationsToRoot implements XmlDocumentCleanerInterface
{
    use XmlNamespaceMethodsTrait;
    use XmlAttributeMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $root = $document->documentElement;
        if (null === $root) {
            return;
        }

        if (! $root->hasAttributeNS(XmlConstants::NAMESPACE_XSI, 'schemaLocation')) {
            $root->setAttributeNS(XmlConstants::NAMESPACE_XSI, 'xsi:schemaLocation', '');
        }
        $rootAttribute = $root->getAttributeNodeNS(XmlConstants::NAMESPACE_XSI, 'schemaLocation');
        $schemaLocation = SchemaLocation::createFromValue((string) $rootAttribute->nodeValue);

        $xpath = Cfdi3XPath::createFromDocument($document);
        $schemaLocationAttributes = $xpath->queryAttributes('//@xsi:schemaLocation');
        foreach ($schemaLocationAttributes as $schemaLocationAttribute) {
            if ($rootAttribute === $schemaLocationAttribute) {
                continue;
            }

            $currentSchemaLocation = SchemaLocation::createFromValue((string) $schemaLocationAttribute->nodeValue);
            $schemaLocation->import($currentSchemaLocation);
            $this->attributeRemove($schemaLocationAttribute);
        }

        $rootAttribute->nodeValue = $schemaLocation->asValue();
    }
}
