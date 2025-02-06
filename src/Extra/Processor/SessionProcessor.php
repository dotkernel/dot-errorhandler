<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Laminas\Stdlib\ArrayUtils;

use function array_map;

class SessionProcessor implements ProcessorInterface
{
    public function process(array $data): array
    {
        return array_map(fn ($container): array => ArrayUtils::iteratorToArray($container), $data);
    }
}
