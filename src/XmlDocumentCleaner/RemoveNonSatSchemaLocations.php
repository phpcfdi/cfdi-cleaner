<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\Internal\SchemaLocation;
use PhpCfdi\CfdiCleaner\Internal\XmlAttributeMethodsTrait;
use PhpCfdi\CfdiCleaner\Internal\XmlNamespaceMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveNonSatSchemaLocations implements XmlDocumentCleanerInterface
{
    use XmlAttributeMethodsTrait;
    use XmlNamespaceMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath = Cfdi3XPath::createFromDocument($document);
        $schemaLocations = $xpath->queryAttributes('//@xsi:schemaLocation');
        foreach ($schemaLocations as $schemaLocation) {
            $value = $this->cleanSchemaLocationsValue($schemaLocation->value);
            $this->attributeSetValueOrRemoveIfEmpty($schemaLocation, $value);
        }
    }

    public function cleanSchemaLocationsValue(string $schemaLocationValue): string
    {
        $schemaLocation = SchemaLocation::createFromValue($schemaLocationValue);
        $schemaLocation->filterUsingNamespace(
            function (string $namespace): bool {
                return $this->isNamespaceRelatedToSat($namespace);
            }
        );
        return $schemaLocation->asValue();
    }
}
