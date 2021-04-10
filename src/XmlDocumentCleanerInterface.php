<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

use DOMDocument;

interface XmlDocumentCleanerInterface
{
    public function clean(DOMDocument $document): void;
}
