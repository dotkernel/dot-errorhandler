<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class ServiceNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
