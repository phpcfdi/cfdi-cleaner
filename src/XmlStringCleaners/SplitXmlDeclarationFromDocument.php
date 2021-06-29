<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class SplitXmlDeclarationFromDocument implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        return preg_replace('#(<\?xml.*?\?>)([\s]*?)<#m', "\$1\n<", $xml) ?: '';
    }
}
