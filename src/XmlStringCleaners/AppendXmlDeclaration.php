<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class AppendXmlDeclaration implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        if (! str_starts_with($xml, '<?xml ')) {
            $xml = '<?xml version="1.0"?>' . "\n" . $xml;
        }
        return $xml;
    }
}
