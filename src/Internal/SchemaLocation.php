<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Internal;

/**
 * Helper class to work with xsi:schemaLocation attribute
 *
 * @internal
 */
final class SchemaLocation
{
    /** @var array<string, string> */
    private $pairs;

    /**
     * SchemaLocation constructor.
     *
     * @param array<string, string> $pairs On each entry: key is namespace, value is location
     */
    public function __construct(array $pairs)
    {
        $this->pairs = $pairs;
    }

    public static function createFromValue(string $value): self
    {
        return self::createFromComponents(self::valueToComponents($value));
    }

    /**
     * @param string $schemaLocationValue
     * @return string[]
     */
    public static function valueToComponents(string $schemaLocationValue): array
    {
        return array_values(array_filter(explode(' ', preg_replace('/\s/', ' ', $schemaLocationValue) ?? '')));
    }

    /**
     * @param string[] $components
     * @return self
     */
    public static function createFromComponents(array $components): self
    {
        $pairs = [];
        $count = count($components);
        for ($i = 0; $i < $count; $i = $i + 2) {
            $pairs[$components[$i]] = $components[$i + 1] ?? '';
        }
        return new self($pairs);
    }

    /** @return array<string, string> */
    public function getPairs(): array
    {
        return $this->pairs;
    }

    public function setPair(string $namespace, string $location): void
    {
        $this->pairs[$namespace] = $location;
    }

    public function filterUsingNamespace(callable $filterFunction): void
    {
        $this->pairs = array_filter($this->pairs, $filterFunction, ARRAY_FILTER_USE_KEY);
    }

    public function asValue(): string
    {
        return implode(' ', array_map(
            function (string $namespace, string $location): string {
                return $namespace . ' ' . $location;
            },
            array_keys($this->pairs),
            $this->pairs,
        ));
    }

    public function import(self $source): void
    {
        $this->pairs = array_merge($this->pairs, $source->pairs);
    }
}
