<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_map;
use function implode;

class HeaderProcessor implements ProcessorInterface
{
    public function process(array $data): array
    {
        return array_map(fn (array $headerSet): string => implode("; ", $headerSet), $data);
    }
}
