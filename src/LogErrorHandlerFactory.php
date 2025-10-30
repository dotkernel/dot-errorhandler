<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Dot\Log\LoggerInterface;
use InvalidArgumentException;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

use function is_array;
use function sprintf;

class LogErrorHandlerFactory
{
    public const ERROR_HANDLER_KEY        = 'dot-errorhandler';
    public const ERROR_HANDLER_LOGGER_KEY = 'logger';

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        $errorHandlerConfig = $container->get('config')[self::ERROR_HANDLER_KEY] ?? null;
        if (! is_array($errorHandlerConfig)) {
            throw new InvalidArgumentException(sprintf('\'[%s\'] not found in config', self::ERROR_HANDLER_KEY));
        }

        if ($errorHandlerConfig['loggerEnabled'] && ! isset($errorHandlerConfig[self::ERROR_HANDLER_LOGGER_KEY])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Logger: \'[%s\'] is enabled, but not found in config',
                    self::ERROR_HANDLER_LOGGER_KEY
                )
            );
        }

        $logger = null;
        if ($errorHandlerConfig['loggerEnabled']) {
            /** @var LoggerInterface $logger */
            $logger = $container->get($errorHandlerConfig[self::ERROR_HANDLER_LOGGER_KEY]);
        }

        $generator = $container->has(ErrorResponseGenerator::class)
            ? $container->get(ErrorResponseGenerator::class)
            : null;

        return new LogErrorHandler($container->get(ResponseInterface::class), $generator, $logger);
    }
}
