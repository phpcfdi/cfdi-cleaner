<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Features;

use PhpCfdi\CfdiCleaner\Document;
use PhpCfdi\CfdiCleaner\Tests\TestCase;
use PhpCfdi\CfdiCleaner\XmlNsSchemaLocation;

class RepairXmlNsSchemaLocationTest extends TestCase
{
    public function testCleaning(): void
    {
        $input = $this->fileContents('xmlns-schemalocation-dirty.xml');
        $document = Document::load($input);

        $cleaner = new XmlNsSchemaLocation();
        $cleaner->clean($document);

        $expectedFilePath = $this->filePath('xmlns-schemalocation-clean.xml');
        $clean = $document->getXmlContents();
        $this->assertXmlStringEqualsXmlFile($expectedFilePath, $clean);
    }
}
