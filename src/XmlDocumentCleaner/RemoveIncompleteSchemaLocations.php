<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\Internal\SchemaLocation;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveIncompleteSchemaLocations implements XmlDocumentCleanerInterface
{
    public function clean(DOMDocument $document): void
    {
        $xpath = Cfdi3XPath::createFromDocument($document);
        $schemaLocations = $xpath->queryAttributes('//@xsi:schemaLocation');
        foreach ($schemaLocations as $schemaLocation) {
            $schemaLocation->value = $this->cleanSchemaLocationValue($schemaLocation->value);
        }
    }

    public function cleanSchemaLocationValue(string $schemaLocationValue): string
    {
        $schemaLocation = new SchemaLocation($this->schemaLocationValueNamespaceXsdPairToArray($schemaLocationValue));
        return $schemaLocation->asValue();
    }

    /**
     * Parses schema location value skipping namespaces without xsd locations (identified by .xsd extension)
     *
     * @param string $schemaLocationValue
     * @return array<string, string>
     */
    public function schemaLocationValueNamespaceXsdPairToArray(string $schemaLocationValue): array
    {
        $components = SchemaLocation::valueToComponents($schemaLocationValue);
        $pairs = [];
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
            $pairs[$namespace] = $location;
            $c = $c + 1; // skip ns declaration
        }

        return $pairs;
    }

    public function uriEndsWithXsd(string $uri): bool
    {
        return str_ends_with(strtolower($uri), '.xsd');
    }
}
