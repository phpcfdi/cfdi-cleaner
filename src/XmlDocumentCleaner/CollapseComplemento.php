<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlDocumentCleaner;

use DOMDocument;
use DOMNode;
use PhpCfdi\CfdiCleaner\Internal\Cfdi3XPath;
use PhpCfdi\CfdiCleaner\XmlDocumentCleanerInterface;

class CollapseComplemento implements XmlDocumentCleanerInterface
{
    public function clean(DOMDocument $document): void
    {
        $xpath3 = Cfdi3XPath::createFromDocument($document);
        $comprobante = $xpath3->queryFirstElement('/cfdi:Comprobante');
        if (null === $comprobante) {
            return;
        }

        $complementos = $xpath3->queryElements('/cfdi:Comprobante/cfdi:Complemento');
        if ($complementos->length < 2) {
            return;
        }

        $first = null;
        /** @var DOMDocument $complemento */
        foreach ($complementos as $complemento) {
            // first complemento
            if (null === $first) {
                $first = $complemento;
                continue;
            }
            // non-first complemento
            $comprobante->removeChild($complemento);
            while ($complemento->childNodes->length > 0) {
                /** @var DOMNode $child */
                $child = $complemento->childNodes->item(0);
                $complemento->removeChild($child);
                $first->appendChild($child);
            }
        }
    }
}
