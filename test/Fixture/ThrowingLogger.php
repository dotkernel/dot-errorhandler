<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Psr\Log\AbstractLogger;
use RuntimeException;
use Stringable;

/**
 * PSR-3 logger that always fails, like a logger writing to a full disk.
 */
final class ThrowingLogger extends AbstractLogger
{
    /**
     * @param array<mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        throw new RuntimeException('disk full');
    }
}
