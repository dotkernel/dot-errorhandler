<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_keys;
use function array_map;
use function array_reduce;
use function count;
use function explode;
use function implode;
use function is_array;
use function preg_split;
use function sprintf;
use function strtolower;

use const PREG_SPLIT_NO_EMPTY;

class ServerProcessor extends AbstractProcessor
{
    /**
     * @param array<array-key, mixed> $data
     * @return array<array-key, mixed>
     */
    public function process(array $data): array
    {
        if (count($data) === 0) {
            return $data;
        }

        $return = [];

        foreach ($data as $serverKey => $serverValue) {
            if ($serverKey === 'HTTP_COOKIE') {
                $serverValue = $this->stringToAssociativeArray($this->stringify($serverValue));
                $serverValue = $this->process($serverValue);
                $serverValue = $this->associativeArrayToString($serverValue);

                $return[$serverKey] = $serverValue;
                continue;
            }

            if (
                ! isset($this->sensitiveParameters[ProcessorInterface::ALL])
                && ! isset($this->sensitiveParameters[strtolower((string) $serverKey)])
            ) {
                $return[$serverKey] = $serverValue;
                continue;
            }

            $return[$serverKey] = is_array($serverValue)
                ? array_map(
                    fn (mixed $item): string => $this->replace($this->replacementStrategy, $this->stringify($item)),
                    $serverValue
                )
                : $this->replace($this->replacementStrategy, $this->stringify($serverValue));
        }

        return $return;
    }

    /**
     * Splits a Cookie header value into name => value pairs.
     * Pairs are separated by ";" with optional whitespace around it.
     *
     * @return array<array-key, string>
     */
    private function stringToAssociativeArray(string $subject): array
    {
        $pairs = preg_split('/\s*;\s*/', $subject, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_reduce($pairs, static function (array $result, string $keyValue): array {
            $keyValue             = explode('=', $keyValue, 2);
            $result[$keyValue[0]] = $keyValue[1] ?? '';

            return $result;
        }, []);
    }

    /**
     * @param array<array-key, mixed> $subject
     */
    private function associativeArrayToString(array $subject): string
    {
        $subject = array_map(
            static fn (int|string $key, string $value): string => sprintf('%s=%s', $key, $value),
            array_keys($subject),
            $subject
        );

        return implode('; ', $subject);
    }
}
