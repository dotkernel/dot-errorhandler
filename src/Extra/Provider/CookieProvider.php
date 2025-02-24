<?php

declare(strict_types=1);

namespace Dot\ErrorHandler\Extra\Provider;

use Dot\ErrorHandler\Extra\Processor\ProcessorInterface;

class CookieProvider extends AbstractProvider
{
    public function provide(array $data): array
    {
        if ($this->processor instanceof ProcessorInterface) {
            return $this->processor->process($data);
        }

        return $data;
    }
}
