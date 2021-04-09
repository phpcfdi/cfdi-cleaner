<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class AppendXmlDeclaration implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        if ('<?xml ' !== substr($xml, 0, 6)) {
            $xml = '<?xml version="1.0"?>' . "\n" . $xml;
        }
        return $xml;
    }
}
