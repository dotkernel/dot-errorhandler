<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function count;
use function implode;
use function is_array;
use function strtolower;

class HeaderProcessor extends AbstractProcessor
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

        foreach ($data as $headerName => $headerValue) {
            if (is_array($headerValue)) {
                $headerValue = implode('; ', $headerValue);
            }
            $name     = strtolower((string) $headerName);
            $isCookie = $name === 'cookie';
            if (
                ! isset($this->sensitiveParameters[ProcessorInterface::ALL])
                && ! isset($this->sensitiveParameters[$name])
                && ! $isCookie
            ) {
                $return[$headerName] = $headerValue;
                continue;
            }

            $return[$headerName] = $isCookie
                ? $this->replaceInlineCookieValues($this->replacementStrategy, $this->stringify($headerValue))
                : $this->replace($this->replacementStrategy, $this->stringify($headerValue));
        }

        return $return;
    }
}
