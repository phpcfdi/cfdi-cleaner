<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\MoveSchemaLocationsToRoot;

final class MoveSchemaLocationsToRootTest extends TestCase
{
    public function testMoveSchemaLocationsToRoot(): void
    {
        $document = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://tempuri.org/root root.xsd http://tempuri.org/bar bar.xsd">
              <foo xsi:schemaLocation="http://tempuri.org/foo foo.xsd">
                <bar xsi:schemaLocation="http://tempuri.org/foo foo.xsd http://tempuri.org/bar bar.xsd"/>
              </foo>
            </root>
            XML);

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expectedLocations = implode(' ', [
            'http://tempuri.org/root root.xsd',
            'http://tempuri.org/bar bar.xsd',
            'http://tempuri.org/foo foo.xsd',
        ]);
        $expected = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="$expectedLocations">
              <foo>
                <bar />
              </foo>
            </root>
            XML);
        $this->assertEquals($expected, $document);
    }

    public function testMoveSchemaLocationsToRootWithDifferentPrefix(): void
    {
        $document = $this->createDocument(<<<XML
            <root xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
              xs:schemaLocation="http://tempuri.org/root root.xsd">
              <foo xs:schemaLocation="http://tempuri.org/foo foo.xsd"/>
            </root>
            XML);

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <root xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
              xs:schemaLocation="http://tempuri.org/root root.xsd http://tempuri.org/foo foo.xsd">
              <foo/>
            </root>
            XML);
        $this->assertEquals($expected, $document);
    }

    public function testMoveSchemaLocationsToRootWithoutRootSchemaLocation(): void
    {
        $document = $this->createDocument(<<<XML
            <root>
              <foo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://tempuri.org/foo foo.xsd"/>
            </root>
            XML);

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="http://tempuri.org/foo foo.xsd">
              <foo/>
            </root>
            XML);
        $this->assertEquals($expected, $document);
    }
}
