<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_map;
use function sprintf;

class TraceProcessor extends AbstractProcessor
{
    public function process(array $data): array
    {
        return array_map(
            /**
             * @param string[] $trace
             */
            fn (array $trace): string => sprintf(
                '%s%s%s:%d',
                $trace['class'] ?? $trace['file'] ?? 'unknown',
                $trace['type'] ?? '->',
                $trace['function'] ?? 'unknown',
                $trace['line'] ?? 0
            ),
            $data
        );
    }
}
