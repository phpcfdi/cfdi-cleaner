<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features\XmlDocumentCleaners;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

final class XmlDocumentWithoutRootElementTest extends TestCase
{
    public function testXmlDocumentWithoutRootElement(): void
    {
        $document = new DOMDocument();
        $expected = $document->saveXML();
        $cleaners = XmlDocumentCleaners::createDefault();
        $cleaners->clean($document);
        $this->assertEquals($expected, $document->saveXML());
    }
}
