<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

use DOMDocument;

class Cleaner
{
    /** @var XmlStringCleaners */
    private $stringCleaners;

    /** @var XmlDocumentCleaners */
    private $xmlCleaners;

    public function __construct(?XmlStringCleaners $stringCleaners = null, ?XmlDocumentCleaners $xmlCleaners = null)
    {
        $this->stringCleaners = $stringCleaners ?? XmlStringCleaners::createDefault();
        $this->xmlCleaners = $xmlCleaners ?? XmlDocumentCleaners::createDefault();
    }

    public static function staticClean(string $xml): string
    {
        return (new self())->cleanStringToString($xml);
    }

    public function cleanString(string $xml): string
    {
        return $this->stringCleaners->clean($xml);
    }

    public function cleanDocument(DOMDocument $document): void
    {
        $this->xmlCleaners->clean($document);
    }

    public function cleanStringToDocument(string $xml): DOMDocument
    {
        $xml = $this->cleanString($xml);
        $document = $this->createDocument($xml);
        $this->cleanDocument($document);
        return $document;
    }

    public function cleanStringToString(string $xml): string
    {
        return $this->cleanStringToDocument($xml)->saveXML() ?: '';
    }

    protected function createDocument(string $xml): DOMDocument
    {
        $document = new DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($xml);
        return $document;
    }
}
