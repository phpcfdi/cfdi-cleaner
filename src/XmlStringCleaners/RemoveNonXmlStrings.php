<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class RemoveNonXmlStrings implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        $posFirstLessThan = strpos($xml, '<');
        if (false === $posFirstLessThan) {
            return '';
        }

        $posLastGreaterThan = strrpos($xml, '>');
        if (false === $posLastGreaterThan) {
            return '';
        }

        $length = $posLastGreaterThan - $posFirstLessThan + 1;
        if ($length <= 0) {
            return '';
        }

        return substr($xml, $posFirstLessThan, $length);
    }
}
