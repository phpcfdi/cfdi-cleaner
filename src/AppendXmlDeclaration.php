<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class AppendXmlDeclaration implements CleanerInterface
{
    public function clean(Document $document): void
    {
        $contents = $document->getXmlContents();
        if ('<?xml ' !== substr($contents, 0, 6)) {
            $contents = '<?xml version="1.0"?>' . "\n" . $contents;
        }
        $document->setXmlContents($contents);
    }
}
