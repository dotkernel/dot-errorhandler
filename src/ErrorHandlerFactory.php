<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function assert;
use function is_callable;

class ErrorHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        $generator = null;
        if ($container->has(ErrorResponseGenerator::class)) {
            $generator = $container->get(ErrorResponseGenerator::class);
            assert($generator instanceof ErrorResponseGenerator);
        }

        $responseInterface = $container->get(ResponseInterface::class);
        assert(is_callable($responseInterface));

        return new ErrorHandler($responseInterface, $generator);
    }
}
