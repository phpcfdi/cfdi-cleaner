<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

class RemoveDuplicatedCfdi3Namespace implements CleanerInterface
{
    public function clean(Document $document): void
    {
        $content = $document->getXmlContents();
        if (false !== strpos($content, 'xmlns="http://www.sat.gob.mx/cfd/3"')
            && false !== strpos($content, 'xmlns:cfdi="http://www.sat.gob.mx/cfd/3"')) {
            $content = preg_replace('#\s*xmlns="http://www.sat.gob.mx/cfd/3"\s*#', ' ', $content) ?? '';
        }
        $document->setXmlContents($content);
    }
}
