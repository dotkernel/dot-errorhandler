<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_keys;
use function array_map;
use function array_reduce;
use function count;
use function explode;
use function implode;
use function sprintf;
use function strtolower;

class ServerProcessor extends AbstractProcessor
{
    public function process(array $data): array
    {
        if (count($data) === 0) {
            return $data;
        }

        $return = [];

        foreach ($data as $serverKey => $serverValue) {
            if ($serverKey === 'HTTP_COOKIE') {
                $serverValue = $this->stringToAssociativeArray($serverValue);
                $serverValue = $this->process($serverValue);
                $serverValue = $this->associativeArrayToString($serverValue);

                $return[$serverKey] = $serverValue;
                continue;
            }

            if (
                ! isset($this->sensitiveParameters[ProcessorInterface::ALL])
                && ! isset($this->sensitiveParameters[strtolower($serverKey)])
            ) {
                $return[$serverKey] = $serverValue;
                continue;
            }

            $return[$serverKey] = $this->replace($this->replacementStrategy, $serverValue);
        }

        return $return;
    }

    private function stringToAssociativeArray(string $subject): array
    {
        return array_reduce(explode('; ', $subject), function (array $result, string $keyValue): array {
            $keyValue             = explode('=', $keyValue, 2);
            $result[$keyValue[0]] = $keyValue[1] ?? '';

            return $result;
        }, []);
    }

    private function associativeArrayToString(array $subject): string
    {
        $subject = array_map(
            fn(string $key, string $value) => sprintf('%s=%s', $key, $value),
            array_keys($subject),
            $subject
        );

        return implode('; ', $subject);
    }
}
