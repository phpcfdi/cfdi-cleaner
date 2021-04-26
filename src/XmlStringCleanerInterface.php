<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

interface XmlStringCleanerInterface
{
    public function clean(string $xml): string;
}
