<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class RemoveDuplicatedCfdi3Namespace implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        if (false !== strpos($xml, 'xmlns="http://www.sat.gob.mx/cfd/3"')
            && false !== strpos($xml, 'xmlns:cfdi="http://www.sat.gob.mx/cfd/3"')) {
            $xml = preg_replace('#\s*xmlns="http://www.sat.gob.mx/cfd/3"\s*#', ' ', $xml) ?? '';
        }
        return $xml;
    }
}