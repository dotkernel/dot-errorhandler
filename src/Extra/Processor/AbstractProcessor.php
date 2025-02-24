<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\ReplacementStrategy;

use function preg_replace_callback;
use function round;
use function sprintf;
use function str_pad;
use function str_repeat;
use function strlen;
use function substr;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function __construct(
        protected array $sensitiveParameters = [],
        protected ReplacementStrategy $replacementStrategy = ReplacementStrategy::Full,
    ) {
    }

    abstract public function process(array $data): array;

    public function getSensitiveParameters(): array
    {
        return $this->sensitiveParameters;
    }

    public function getReplacementStrategy(): ReplacementStrategy
    {
        return $this->replacementStrategy;
    }

    public function replace(
        ReplacementStrategy $replacementStrategy,
        string $subject,
        string $replacement = ProcessorInterface::ALL
    ): string {
        return match ($replacementStrategy->name) {
            ReplacementStrategy::Full->name    => $this->replaceFull($subject, $replacement),
            ReplacementStrategy::Partial->name => $this->replacePartial($subject, $replacement),
        };
    }

    private function replaceFull(string $subject, string $replacement = ProcessorInterface::ALL): string
    {
        return str_repeat($replacement, strlen($subject));
    }

    private function replacePartial(string $subject, string $replacement = ProcessorInterface::ALL): string
    {
        return str_pad(substr($subject, 0, (int) round(strlen($subject) / 2)), strlen($subject), $replacement);
    }

    public function replaceInlineCookieValues(ReplacementStrategy $replacementStrategy, string $header): string
    {
        return (string) preg_replace_callback(
            '/([^=\s;]+)=([^;]*)/',
            fn (array $matches): string => sprintf(
                '%s=%s',
                $matches[1] ?? '',
                $this->replace($replacementStrategy, $matches[2] ?? '')
            ),
            $header
        );
    }
}
