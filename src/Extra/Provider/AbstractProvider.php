<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Provider;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;

abstract class AbstractProvider implements ProviderInterface
{
    public function __construct(
        protected bool $enabled = false,
        protected ?ProcessorInterface $processor = null,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getProcessor(): ?ProcessorInterface
    {
        return $this->processor;
    }

    abstract public function provide(array $data): array;
}
