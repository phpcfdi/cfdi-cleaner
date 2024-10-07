<?php

declare(strict_types=1);

namespace PhpCfdi\CfdiCleaner\Tests\Traits;

use RuntimeException;
use Throwable;

trait UseSatNsRegistryTrait
{
    /** @var array<object{namespace: ?string, prefix: ?string, version: ?string, xsd: ?string}>|null */
    private $satNsRegistry = null;

    /** @return array<object{namespace: ?string, prefix: ?string, version: ?string, xsd: ?string}> */
    protected function getSatNsRegistry(): array
    {
        if (null === $this->satNsRegistry) {
            $this->satNsRegistry = $this->loadSatNsRegistry();
        }

        return $this->satNsRegistry;
    }

    /** @return array<object{namespace: ?string, prefix: ?string, version: ?string, xsd: ?string}> */
    protected function loadSatNsRegistry(): array
    {
        // obtain the list of known locations from phpcfdi/sat-ns-registry
        $satNsRegistryUrl = 'https://raw.githubusercontent.com/phpcfdi/sat-ns-registry/master/complementos_v1.json';
        try {
            /** @var array<object{namespace: ?string, prefix: ?string, version: ?string, xsd: ?string}> $registry */
            $registry = json_decode((string) file_get_contents($satNsRegistryUrl), false, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            $message = sprintf('Unable to load SAT Namespaces Registry from %s', $satNsRegistryUrl);
            throw new RuntimeException($message, 0, $exception);
        }

        return $registry;
    }
}
