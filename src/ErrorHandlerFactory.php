<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        $generator = $container->has('Mezzio\Middleware\ErrorResponseGenerator')
            ? $container->get('Mezzio\Middleware\ErrorResponseGenerator')
            : null;

        return new ErrorHandler($container->get(ResponseInterface::class), $generator);
    }
}
