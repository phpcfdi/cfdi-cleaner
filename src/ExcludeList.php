<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class to specify the cleaner class names to be excluded
 * @see Cleaner::exclude()
 * @implements IteratorAggregate<int, class-string>
 */
final class ExcludeList implements IteratorAggregate
{
    /** @var list<class-string> */
    private $classNames;

    /** @param class-string ...$classNames */
    public function __construct(string ...$classNames)
    {
        $this->classNames = array_values($classNames);
    }

    public function isEmpty(): bool
    {
        return [] === $this->classNames;
    }

    public function match(object $object): bool
    {
        foreach ($this->classNames as $className) {
            if ($object instanceof $className) {
                return true;
            }
        }

        return false;
    }

    /**
     * @template TObject of object
     * @param TObject ...$objects
     * @return array<TObject>
     */
    public function filterObjects(object ...$objects): array
    {
        return array_filter(
            $objects,
            function (object $object): bool {
                return ! $this->match($object);
            }
        );
    }

    /** @return Traversable<int, class-string> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->classNames);
    }
}
