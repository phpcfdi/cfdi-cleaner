<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Internal;

use DOMElement;

/**
 * @internal
 */
trait XmlElementMethodsTrait
{
    private function elementRemove(DOMElement $element): void
    {
        $parent = $element->parentNode;
        if (null !== $parent) {
            $parent->removeChild($element);
        }
    }

    private function elementMove(DOMElement $element, DOMElement $parent): void
    {
        $this->elementRemove($element);
        $parent->appendChild($element);
    }
}
