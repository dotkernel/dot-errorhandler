<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Provider;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;

readonly class ServerProvider
{
    public function __construct(
        public bool $enabled = false,
        public ?ProcessorInterface $processor = null,
    ) {
    }

    public function provide(array $server): array
    {
        if ($this->processor instanceof ProcessorInterface) {
            return $this->processor->process($server);
        }

        return $server;
    }
}
