<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;

/**
 * Minimal PSR-11 container: one entry per service id, no factories.
 */
final class InMemoryContainer implements ContainerInterface
{
    /**
     * @param array<string, mixed> $services
     */
    public function __construct(
        private readonly array $services = [],
    ) {
    }

    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            throw new ServiceNotFoundException(sprintf('Service "%s" not found', $id));
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
