<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\ReplacementStrategy;

interface ProcessorInterface
{
    public const ALL = '*';

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    public function process(array $data): array;

    /**
     * @return array<array-key, int> sensitive names, lower-cased, as keys
     */
    public function getSensitiveParameters(): array;

    public function getReplacementStrategy(): ReplacementStrategy;

    public function replace(
        ReplacementStrategy $replacementStrategy,
        string $subject,
        string $replacement = ProcessorInterface::ALL
    ): string;

    public function replaceInlineCookieValues(ReplacementStrategy $replacementStrategy, string $header): string;
}
