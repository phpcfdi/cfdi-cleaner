<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class RemoveNonXmlStrings implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        return preg_replace(['/^(\s|.)*?</m', '/>(?:(.|\s)(?!>))*$/m'], ['<', '>'], $xml) ?? '';
    }
}
