<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Dot\ErrorHandler\Extra\Processor\AbstractProcessor;
use RuntimeException;

/**
 * Extra-data processor that always fails, like a buggy custom processor.
 */
final class ThrowingProcessor extends AbstractProcessor
{
    /**
     * @param array<array-key, mixed> $data
     */
    public function process(array $data): never
    {
        throw new RuntimeException('processor failure');
    }
}
