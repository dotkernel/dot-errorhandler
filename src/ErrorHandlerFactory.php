<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use InvalidArgumentException;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use function is_callable;
use function sprintf;

class ErrorHandlerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException When a configured service is not callable.
     */
    public function __invoke(ContainerInterface $container): ErrorHandler
    {
        $generator = $container->has(ErrorResponseGenerator::class)
            ? $container->get(ErrorResponseGenerator::class)
            : null;
        if ($generator !== null && ! is_callable($generator)) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" must be callable', ErrorResponseGenerator::class)
            );
        }

        $responseFactory = $container->get(ResponseInterface::class);
        if (! is_callable($responseFactory)) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" must be a callable response factory', ResponseInterface::class)
            );
        }

        return new ErrorHandler($responseFactory, $generator);
    }
}
