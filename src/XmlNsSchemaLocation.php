<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class XmlNsSchemaLocation implements CleanerInterface
{
    public function clean(Document $document): void
    {
        $document->setXmlContents(
            preg_replace('/(\s)xmlns:schemaLocation="/', '$1xsi:schemaLocation="', $document->getXmlContents()) ?? ''
        );
    }
}
