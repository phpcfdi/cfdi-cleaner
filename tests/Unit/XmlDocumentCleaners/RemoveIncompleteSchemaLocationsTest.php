<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Unit\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RemoveIncompleteSchemaLocations;

final class RemoveIncompleteSchemaLocationsTest extends TestCase
{
    public function testSchemaLocationValueNamespaceXsdPairToArray(): void
    {
        $input = <<< XSISL
                http://tempuri.org/root http://tempuri.org/root.xsd
                http://tempuri.org/foo
                http://tempuri.org/bar http://tempuri.org/bar.xsd
                                       http://tempuri.org/one.xsd
                http://tempuri.org/two http://tempuri.org/two.xsd
                http://tempuri.org/three http://tempuri.org/three
            XSISL;

        $expectedPairs = [
            'http://tempuri.org/root' => 'http://tempuri.org/root.xsd',
            'http://tempuri.org/bar' => 'http://tempuri.org/bar.xsd',
            'http://tempuri.org/two' => 'http://tempuri.org/two.xsd',
        ];

        $cleaner = new RemoveIncompleteSchemaLocations();
        $pairs = $cleaner->schemaLocationValueNamespaceXsdPairToArray($input);
        $this->assertEquals($expectedPairs, $pairs);
    }

    /**
     * @param string $uri
     * @param bool $expected
     * @testWith ["location", false]
     *           ["locationxsd", false]
     *           ["", false]
     *           ["location.xsd", true]
     *           ["location.XSD", true]
     *           ["location.Xsd", true]
     *           ["location..xsd", true]
     */
    public function testUriEndsWithXsd(string $uri, bool $expected): void
    {
        $cleaner = new RemoveIncompleteSchemaLocations();
        $this->assertSame($expected, $cleaner->uriEndsWithXsd($uri));
    }
}
