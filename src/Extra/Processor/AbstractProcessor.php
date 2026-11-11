<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use Dot\ErrorHandler\Extra\ReplacementStrategy;
use Stringable;

use function get_debug_type;
use function is_scalar;
use function mb_strlen;
use function mb_substr;
use function preg_replace_callback;
use function round;
use function sprintf;
use function str_repeat;

abstract class AbstractProcessor implements ProcessorInterface
{
    /**
     * @param array<array-key, int> $sensitiveParameters sensitive names, lower-cased, as keys
     */
    public function __construct(
        protected array $sensitiveParameters = [],
        protected ReplacementStrategy $replacementStrategy = ReplacementStrategy::Full,
    ) {
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    abstract public function process(array $data): array;

    /**
     * @return array<array-key, int>
     */
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
        return match ($replacementStrategy) {
            ReplacementStrategy::Full    => $this->replaceFull($subject, $replacement),
            ReplacementStrategy::Partial => $this->replacePartial($subject, $replacement),
        };
    }

    /**
     * Lengths are counted in characters, so that masking never splits a multibyte character.
     */
    private function replaceFull(string $subject, string $replacement = ProcessorInterface::ALL): string
    {
        return str_repeat($replacement, mb_strlen($subject));
    }

    private function replacePartial(string $subject, string $replacement = ProcessorInterface::ALL): string
    {
        $length = mb_strlen($subject);
        $keep   = (int) round($length / 2);

        return mb_substr($subject, 0, $keep) . str_repeat($replacement, $length - $keep);
    }

    /**
     * String form of any value, for masking: scalars and Stringable objects as themselves,
     * anything else as its type name, so that processing never throws or warns.
     */
    protected function stringify(mixed $value): string
    {
        return match (true) {
            $value === null, is_scalar($value) => (string) $value,
            $value instanceof Stringable       => (string) $value,
            default                            => get_debug_type($value),
        };
    }

    public function replaceInlineCookieValues(ReplacementStrategy $replacementStrategy, string $header): string
    {
        return (string) preg_replace_callback(
            '/([^=\s;]+)=([^;]*)/',
            fn (array $matches): string => sprintf(
                '%s=%s',
                $matches[1],
                $this->replace($replacementStrategy, $matches[2])
            ),
            $header
        );
    }
}
