<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Provider;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;

interface ProviderInterface
{
    public function isEnabled(): bool;

    public function getProcessor(): ?ProcessorInterface;

    public function provide(array $data): array;
}
