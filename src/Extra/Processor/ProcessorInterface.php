<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\ReplacementStrategy;

interface ProcessorInterface
{
    public const ALL = '*';

    public function process(array $data): array;

    public function getSensitiveParameters(): array;

    public function getReplacementStrategy(): ReplacementStrategy;

    public function replace(
        ReplacementStrategy $replacementStrategy,
        string $subject,
        string $replacement = ProcessorInterface::ALL
    ): string;

    public function replaceInlineCookieValues(ReplacementStrategy $replacementStrategy, string $header): string;
}
