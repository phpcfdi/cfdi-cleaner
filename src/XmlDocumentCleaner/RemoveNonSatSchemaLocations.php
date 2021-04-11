<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveNonSatSchemaLocations implements XmlDocumentCleanerInterface
{
    public function clean(DOMDocument $document): void
    {
        $xpath = Cfdi3XPath::createFromDocument($document);
        $schemaLocations = $xpath->queryAttributes('//@xsi:schemaLocation');
        foreach ($schemaLocations as $schemaLocation) {
            $value = $this->cleanSchemaLocationsValue($schemaLocation->value);
            if ('' === $value) {
                $schemaLocation->ownerElement->removeAttributeNode($schemaLocation);
            } else {
                $schemaLocation->value = $value;
            }
        }
    }

    public function cleanSchemaLocationsValue(string $schemaLocationValue): string
    {
        $components = $this->schemaLocationValueToArray($schemaLocationValue);

        $schemaLocations = [];
        $count = count($components);
        for ($i = 0; $i < $count; $i = $i + 2) {
            $schemaLocations[$components[$i]] = $components[$i + 1] ?? '';
        }

        $schemaLocations = array_filter(
            $schemaLocations,
            function (string $namespace): bool {
                return $this->isNamespaceAllowed($namespace);
            },
            ARRAY_FILTER_USE_KEY
        );

        return implode(' ', array_map(
            function (string $namespace, string $location): string {
                return $namespace . ' ' . $location;
            },
            array_keys($schemaLocations),
            $schemaLocations,
        ));
    }

    public function isNamespaceAllowed(string $namespace): bool
    {
        return str_starts_with($namespace, 'http://www.sat.gob.mx/')
            || str_starts_with($namespace, 'http://www.w3.org/');
    }

    /**
     * @param string $schemaLocationValue
     * @return string[]
     */
    public function schemaLocationValueToArray(string $schemaLocationValue): array
    {
        return array_values(array_filter(explode(' ', preg_replace('/\s/', ' ', $schemaLocationValue) ?? '')));
    }
}
