<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class XmlStringCleaners implements XmlStringCleanerInterface
{
    /** @var XmlStringCleanerInterface[] */
    private $cleaners;

    public function __construct(XmlStringCleanerInterface ...$cleaners)
    {
        $this->cleaners = $cleaners;
    }

    public static function createDefault(): self
    {
        return new self(...[
            new XmlStringCleaners\RemoveNonXmlStrings(),
            new XmlStringCleaners\SplitXmlDeclarationFromDocument(),
            new XmlStringCleaners\AppendXmlDeclaration(),
            new XmlStringCleaners\XmlNsSchemaLocation(),
            // new XmlStringCleaners\RemoveDuplicatedCfdi3Namespace(),
        ]);
    }

    public function clean(string $xml): string
    {
        foreach ($this->cleaners as $cleaner) {
            $xml = $cleaner->clean($xml);
        }
        return $xml;
    }
}
