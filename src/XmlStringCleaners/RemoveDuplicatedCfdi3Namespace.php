<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlDocumentCleaners\RenameElementAddPrefix;
use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

/**
 * @deprecated 1.2.0:2.0.0
 * @see RenameElementAddPrefix
 */
class RemoveDuplicatedCfdi3Namespace implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        trigger_error(
            sprintf('Class %s is deprecated, use %s', self::class, RenameElementAddPrefix::class),
            E_USER_DEPRECATED,
        );
        return $xml;
    }
}
