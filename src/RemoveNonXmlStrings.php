<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class RemoveNonXmlStrings implements CleanerInterface
{
    public function clean(Document $document): void
    {
        $document->setXmlContents(
            preg_replace(
                ['/^(\s|.)*?</m', '/>(?:(.|\s)(?!>))*$/m'],
                ['<', '>'],
                $document->getXmlContents()
            ) ?? ''
        );
    }
}
