<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\MoveNamespaceDeclarationToRoot;

final class MoveNamespaceDeclarationToRootTest extends TestCase
{
    public function testMoveNamespaceDeclarationToRoot(): void
    {
        $document = $this->createDocument(<<<XML
            <r:root xmlns:r="uri:root">
              <foo:foo xmlns:foo="uri:foo"/>
              <bar:bar xmlns:bar="uri:bar"/>
              <xee/>
            </r:root>
            XML
        );

        $cleaner = new MoveNamespaceDeclarationToRoot();
        $cleaner->clean($document);

        $expected = $this->createDocument(<<<XML
            <r:root xmlns:r="uri:root" xmlns:foo="uri:foo" xmlns:bar="uri:bar">
              <foo:foo/>
              <bar:bar/>
              <xee/>
            </r:root>
            XML
        );
        $this->assertEquals($expected, $document);
    }
}
