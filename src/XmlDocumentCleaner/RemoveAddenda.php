<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\XmlElementMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveAddenda implements XmlDocumentCleanerInterface
{
    use XmlElementMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $addendas = $document->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Addenda');
        foreach ($addendas as $addenda) {
            $this->elementRemove($addenda);
        }
    }
}
