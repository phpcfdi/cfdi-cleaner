<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\XmlStringCleaners;

use PhpCfdi\CfdiCleaner\XmlStringCleanerInterface;

class XmlNsSchemaLocation implements XmlStringCleanerInterface
{
    public function clean(string $xml): string
    {
        if (! str_contains($xml, 'xmlns:schemaLocation="')) {
            // nothing to do
            return $xml;
        }

        preg_match_all('/<(?>\s|.)*?>/u', $xml, $matches);
        /** @var array<int, string> $parts */
        $parts = preg_split('/<(?>\s|.)*?>/u', $xml);

        $buffer = [$parts[0]];
        foreach ($matches[0] as $index => $match) {
            $buffer[] = $this->cleanTagContent($match);
            $buffer[] = $parts[$index + 1];
        }

        return implode('', $buffer);
    }

    private function cleanTagContent(string $content): string
    {
        if (! str_contains($content, 'xmlns:schemaLocation="')) {
            // nothing to do
            return $content;
        }

        if (! str_contains($content, 'xsi:schemaLocation="')) {
            // safely replace to "xsi:schemaLocation"
            return preg_replace('/(\s)xmlns:schemaLocation="/', '$1xsi:schemaLocation="', $content) ?? '';
        }

        // remove xmlns:schemaLocation attribute
        return preg_replace('/(\s)*xmlns:schemaLocation="(.|\s)*?"/', '', $content) ?? '';
    }
}
