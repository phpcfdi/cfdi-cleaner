<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveIncompleteSchemaLocations implements XmlDocumentCleanerInterface
{
    public function clean(DOMDocument $document): void
    {
        $xpath = Cfdi3XPath::createFromDocument($document);
        $schemaLocations = $xpath->queryAttributes('//@xsi:schemaLocation');
        foreach ($schemaLocations as $schemaLocation) {
            $schemaLocation->value = $this->cleanSchemaLocationsValue($schemaLocation->value);
        }
    }

    public function cleanSchemaLocationsValue(string $schemaLocationValue): string
    {
        $schemaLocations = $this->schemaLocationValueNamespaceXsdPairToArray($schemaLocationValue);
        return implode(' ', array_map(
            function (string $namespace, string $location): string {
                return $namespace . ' ' . $location;
            },
            array_keys($schemaLocations),
            $schemaLocations,
        ));
    }

    /**
     * Parses schema location value skipping namespaces without xsd locations (identified by .xsd extension)
     *
     * @param string $schemaLocationValue
     * @return array<string, string>
     */
    public function schemaLocationValueNamespaceXsdPairToArray(string $schemaLocationValue): array
    {
        $schemaLocations = [];
        $components = $this->schemaLocationValueToArray($schemaLocationValue);
        $length = count($components);
        for ($c = 0; $c < $length; $c = $c + 1) {
            $namespace = $components[$c];
            if ($this->uriEndsWithXsd($namespace)) { // namespace is a location
                continue;
            }

            $location = $components[$c + 1] ?? '';
            if (! $this->uriEndsWithXsd($location)) { // location is a namespace
                continue;
            }

            // namespace match with location that ends with xsd
            $schemaLocations[$namespace] = $location;
            $c = $c + 1; // skip ns declaration
        }

        return $schemaLocations;
    }

    public function uriEndsWithXsd(string $uri): bool
    {
        return str_ends_with(strtolower($uri), '.xsd');
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
