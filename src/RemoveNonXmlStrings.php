<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class RemoveNonXmlStrings implements CleanerInterface
{
    public function clean(string $xml): string
    {
        return preg_replace(['/^(\s|.)*?</m', '/>(?:(.|\s)(?!>))*$/m'], ['<', '>'], $xml) ?? '';
    }
}
