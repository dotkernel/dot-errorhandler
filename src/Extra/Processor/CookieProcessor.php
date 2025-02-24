<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function count;
use function strtolower;

class CookieProcessor extends AbstractProcessor
{
    public function process(array $data): array
    {
        if (count($this->sensitiveParameters) === 0 || count($data) === 0) {
            return $data;
        }

        $return = [];

        foreach ($data as $cookieName => $cookieValue) {
            if (
                ! isset($this->sensitiveParameters[ProcessorInterface::ALL])
                && ! isset($this->sensitiveParameters[strtolower($cookieName)])
            ) {
                $return[$cookieName] = $cookieValue;
                continue;
            }

            $return[$cookieName] = $this->replace($this->replacementStrategy, $cookieValue);
        }

        return $return;
    }
}
