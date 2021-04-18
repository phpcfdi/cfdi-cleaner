<?php

/** @noinspection XmlPathReference */

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaner;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaner\MoveSchemaLocationsToRoot;

final class MoveSchemaLocationsToRootTest extends TestCase
{
    public function testMoveSchemaLocationsToRoot(): void
    {
        $document = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="uri:root root.xsd uri:bar bar.xsd">
              <foo xsi:schemaLocation="uri:foo foo.xsd">
                <bar xsi:schemaLocation="uri:foo foo.xsd uri:bar bar.xsd"/>
              </foo>
            </root>
            XML
        );

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
              xsi:schemaLocation="uri:root root.xsd uri:bar bar.xsd uri:foo foo.xsd">
              <foo>
                <bar />
              </foo>
            </root>
            XML
        );
        $this->assertEquals($expected, $document);
    }

    public function testMoveSchemaLocationsToRootWithDifferentPrefix(): void
    {
        $document = $this->createDocument(<<<XML
            <root xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
              xs:schemaLocation="uri:root root.xsd">
              <foo xs:schemaLocation="uri:foo foo.xsd"/>
            </root>
            XML
        );

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <root xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
              xs:schemaLocation="uri:root root.xsd uri:foo foo.xsd">
              <foo/>
            </root>
            XML
        );
        $this->assertEquals($expected, $document);
    }

    public function testMoveSchemaLocationsToRootWithoutRootSchemaLocation(): void
    {
        $document = $this->createDocument(<<<XML
            <root>
              <foo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="uri:foo foo.xsd"/>
            </root>
            XML
        );

        $cleaner = new MoveSchemaLocationsToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <root xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="uri:foo foo.xsd">
              <foo/>
            </root>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
