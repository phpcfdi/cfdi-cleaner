<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

interface CleanerInterface
{
    public function clean(Document $document): void;
}
