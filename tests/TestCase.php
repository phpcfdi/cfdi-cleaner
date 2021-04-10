<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests;

use DOMDocument;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function filePath(string $filename): string
    {
        return __DIR__ . '/_files/' . $filename;
    }

    public static function fileContents(string $filename): string
    {
        return file_get_contents(static::filePath($filename)) ?: '';
    }

    protected function createDocument(string $xml): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($xml);
        return $document;
    }
}
