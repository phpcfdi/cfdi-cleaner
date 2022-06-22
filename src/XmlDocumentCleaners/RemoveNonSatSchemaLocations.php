<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\CfdiXPath;
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
        $xpath = CfdiXPath::createFromDocument($document);
        $schemaLocations = $xpath->querySchemaLocations();
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
            },
        );
        return $schemaLocation->asValue();
    }
}
