<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class Document
{
    /** @var string */
    private $xmlContents;

    private function __construct(string $xmlContents)
    {
        $this->xmlContents = $xmlContents;
    }

    public static function load(string $xmlContents): self
    {
        return new self($xmlContents);
    }

    public function getXmlContents(): string
    {
        return $this->xmlContents;
    }

    public function setXmlContents(string $xmlContents): self
    {
        $this->xmlContents = $xmlContents;
        return $this;
    }
}
