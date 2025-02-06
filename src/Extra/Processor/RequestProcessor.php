<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function is_array;
use function is_string;
use function preg_replace;
use function str_contains;
use function strtolower;

class RequestProcessor implements ProcessorInterface
{
    public function process(array $data): array
    {
        $return = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $return[$key] = $this->process($value);
            } elseif (is_string($value)) {
                $lowerKey = strtolower($key);
                if (
                    str_contains($lowerKey, 'password') ||
                    str_contains($lowerKey, 'key') ||
                    str_contains($lowerKey, 'csrf') ||
                    str_contains($lowerKey, 'token')
                ) {
                    $return[$key] = preg_replace('/[\da-z]/i', 'x', $value);
                } else {
                    $return[$key] = $value;
                }
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }
}
