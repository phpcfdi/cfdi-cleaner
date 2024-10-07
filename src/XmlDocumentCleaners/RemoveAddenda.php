<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use PhpCfdi\CfdiCleaner\Internal\XmlElementMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class RemoveAddenda implements XmlDocumentCleanerInterface
{
    use XmlElementMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $this->removeAddendas($document, 'http://www.sat.gob.mx/cfd/3');
        $this->removeAddendas($document, 'http://www.sat.gob.mx/cfd/4');
        $this->removeAddendas($document, 'http://www.sat.gob.mx/esquemas/retencionpago/2');
        $this->removeAddendas($document, 'http://www.sat.gob.mx/esquemas/retencionpago/1');
    }

    private function removeAddendas(DOMDocument $document, string $namespace): void
    {
        $addendas = $document->getElementsByTagNameNS($namespace, 'Addenda');
        foreach ($addendas as $addenda) {
            $this->elementRemove($addenda);
        }
    }
}
