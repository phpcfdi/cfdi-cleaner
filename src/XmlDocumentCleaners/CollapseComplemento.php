<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaners;

use DOMDocument;
use DOMElement;
use PhpCfdi\CfdiCleaner\Internal\CfdiXPath;
use PhpCfdi\CfdiCleaner\Internal\XmlElementMethodsTrait;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class CollapseComplemento implements XmlDocumentCleanerInterface
{
    use XmlElementMethodsTrait;

    public function clean(DOMDocument $document): void
    {
        $xpath3 = CfdiXPath::createFromDocument($document);

        $complementos = $xpath3->queryElements('/cfdi:Comprobante/cfdi:Complemento');
        if ($complementos->length < 2) {
            return;
        }

        $receiver = null;
        foreach ($complementos as $complemento) {
            // first complemento
            if (null === $receiver) {
                $receiver = $complemento;
                continue;
            }

            // non-first complemento
            while ($complemento->childNodes->length > 0) {
                /** @var DOMElement $child */
                $child = $complemento->childNodes->item(0);
                $this->elementMove($child, $receiver);
            }
            $this->elementRemove($complemento);
        }
    }
}
