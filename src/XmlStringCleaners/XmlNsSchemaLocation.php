<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class XmlNsSchemaLocation implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        return preg_replace('/(\s)xmlns:schemaLocation="/', '$1xsi:schemaLocation="', $xml) ?? '';
    }
}
