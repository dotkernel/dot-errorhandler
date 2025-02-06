<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

interface ProcessorInterface
{
    public function process(array $data): array;
}
