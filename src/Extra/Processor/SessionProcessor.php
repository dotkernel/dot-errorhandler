<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Laminas\Stdlib\ArrayUtils;

use function array_map;
use function is_iterable;

class SessionProcessor extends AbstractProcessor
{
    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    public function process(array $data): array
    {
        return array_map(
            static fn (mixed $value): mixed => is_iterable($value) ? ArrayUtils::iteratorToArray($value) : $value,
            $data
        );
    }
}
