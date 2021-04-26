<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Internal;

use DOMAttr;

/**
 * @internal
 */
trait XmlAttributeMethodsTrait
{
    private function attributeRemove(DOMAttr $attribute): void
    {
        $attribute->ownerElement->removeAttribute($attribute->nodeName);
    }

    private function attributeSetValueOrRemoveIfEmpty(DOMAttr $attribute, string $value): void
    {
        if ('' === $value) {
            $this->attributeRemove($attribute);
            return;
        }

        $attribute->value = $value;
    }
}
