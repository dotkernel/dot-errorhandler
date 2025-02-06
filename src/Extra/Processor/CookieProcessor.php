<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Processor;

use function array_map;
use function str_pad;
use function strlen;
use function substr;

class CookieProcessor implements ProcessorInterface
{
    public function process(array $data): array
    {
        return array_map(fn (string $cookie): string => str_pad(substr($cookie, 0, 8), strlen($cookie), '.'), $data);
    }
}
