<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * PSR-3 logger that keeps every record in memory.
 */
final class TestLogger extends AbstractLogger
{
    /** @var list<array{level: mixed, message: string, context: array<mixed>}> */
    public array $records = [];

    /**
     * @param array<mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level'   => $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}
