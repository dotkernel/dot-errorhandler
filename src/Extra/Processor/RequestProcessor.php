<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_filter;
use function array_keys;
use function count;
use function is_array;
use function str_contains;
use function strtolower;

class RequestProcessor extends AbstractProcessor
{
    public function process(array $data): array
    {
        if (count($this->sensitiveParameters) === 0 || count($data) === 0) {
            return $data;
        }

        $return = [];

        $sensitiveParameters = array_keys($this->sensitiveParameters);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $return[$key] = $this->process($value);
            } else {
                $matches = array_filter(
                    $sensitiveParameters,
                    fn (string $sensitiveParameter) => str_contains(strtolower($key), $sensitiveParameter)
                );

                if (! isset($this->sensitiveParameters[ProcessorInterface::ALL]) && count($matches) === 0) {
                    $return[$key] = $value;
                    continue;
                }

                $return[$key] = $this->replace($this->replacementStrategy, (string) $value);
            }
        }

        return $return;
    }
}
