<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

class ServerProcessor implements ProcessorInterface
{
    public function process(array $data): array
    {
        return $data;
    }
}
