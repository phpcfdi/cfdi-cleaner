<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\RemoveIncompleteSchemaLocations;

class RemoveIncompleteSchemaLocationsTest extends TestCase
{
    public function testCleanSchemaLocationsWithIncompletePairsOnlyOnRoot(): void
    {
        // content has incomplete schema location "foo"
        $document = $this->createDocument(<<< XML
            <r xmlns="http://tempuri.org/r" xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
            x:schemaLocation="http://tempuri.org/r r.xsd http://tempuri.org/foo http://tempuri.org/bar bar.xsd"
            />
            XML
        );
        $expected = $this->createDocument(<<< XML
            <r xmlns="http://tempuri.org/r" xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
            x:schemaLocation="http://tempuri.org/r r.xsd http://tempuri.org/bar bar.xsd"
            />
            XML
        );

        $cleaner = new RemoveIncompleteSchemaLocations();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document);
    }

    public function testCleanSchemaLocationsWithIncompletePairsOnlyOnChildren(): void
    {
        // content has incomplete schema location "foo"
        $document = $this->createDocument(<<< XML
            <root>
            <child xmlns="http://tempuri.org/r" xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
            x:schemaLocation="
             http://tempuri.org/foo            foo.xsd
             http://tempuri.org/remove-first
             http://tempuri.org/bar            bar.xsd
             http://tempuri.org/remove-other
             http://tempuri.org/remove-ns      http://tempuri.org/remove-non-xsd  "
             />
            </root>
            XML
        );
        $expected = $this->createDocument(<<< XML
            <root>
            <child xmlns="http://tempuri.org/r" xmlns:x="http://www.w3.org/2001/XMLSchema-instance"
            x:schemaLocation="http://tempuri.org/foo foo.xsd http://tempuri.org/bar bar.xsd"/>
            </root>
            XML
        );

        $cleaner = new RemoveIncompleteSchemaLocations();
        $cleaner->clean($document);

        $this->assertEquals($expected, $document);
    }
}
