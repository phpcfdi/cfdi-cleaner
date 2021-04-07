<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class XmlNsSchemaLocation implements CleanerInterface
{
    public function clean(Document $document): void
    {
        $document->setXmlContents(
            str_replace(' xmlns:schemaLocation="', ' xsi:schemaLocation="', $document->getXmlContents())
        );
    }
}
