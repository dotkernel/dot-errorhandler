<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function count;
use function implode;
use function is_array;
use function strtolower;

class HeaderProcessor extends AbstractProcessor
{
    public function process(array $data): array
    {
        if (count($data) === 0) {
            return $data;
        }

        $return = [];

        foreach ($data as $headerName => $headerValue) {
            if (is_array($headerValue)) {
                $headerValue = implode('; ', $headerValue);
            }
            if (
                ! isset($this->sensitiveParameters[ProcessorInterface::ALL])
                && ! isset($this->sensitiveParameters[strtolower($headerName)])
                && $headerName !== 'cookie'
            ) {
                $return[$headerName] = $headerValue;
                continue;
            }

            $return[$headerName] = $headerName === 'cookie'
                ? $this->replaceInlineCookieValues($this->replacementStrategy, $headerValue)
                : $this->replace($this->replacementStrategy, (string) $headerValue);
        }

        return $return;
    }
}
