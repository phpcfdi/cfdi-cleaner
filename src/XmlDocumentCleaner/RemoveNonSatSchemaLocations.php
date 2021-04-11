<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\Internal\SchemaLocation;
use PhpCfdi\CfdiCleaner\Internal\XmlAttributeMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveNonSatSchemaLocations implements XmlDocumentCleanerInterface
{
    use XmlAttributeMethodsTrait;

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
                return $this->isNamespaceAllowed($namespace);
            }
        );
        return $schemaLocation->asValue();
    }

    public function isNamespaceAllowed(string $namespace): bool
    {
        return str_starts_with($namespace, 'http://www.sat.gob.mx/');
    }
}
