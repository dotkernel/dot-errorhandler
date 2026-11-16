<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler\Fixture;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Request handler that always throws the given Throwable.
 */
final class ThrowingHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly Throwable $throwable,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        throw $this->throwable;
    }
}
