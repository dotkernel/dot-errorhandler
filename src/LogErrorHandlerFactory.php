<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Laminas\Log\LoggerInterface;

class LogErrorHandlerFactory
{
    const ERROR_HANDLER_KEY = 'dot-errorhandler';
    const ERROR_HANDLER_LOGGER_KEY = 'logger';

    public function __invoke(ContainerInterface $container) : MiddlewareInterface
    {
        $errorHandlerConfig = $container->get('config')[self::ERROR_HANDLER_KEY] ?? null;
        if (!is_array($errorHandlerConfig)) {
            throw new InvalidArgumentException(sprintf('\'[%s\'] not found in config', self::ERROR_HANDLER_KEY));
        }

        if ($errorHandlerConfig['loggerEnabled'] && !isset($errorHandlerConfig[self::ERROR_HANDLER_LOGGER_KEY])) {
            throw new InvalidArgumentException(sprintf('Logger: \'[%s\'] is enabled, but not found in config', self::ERROR_HANDLER_LOGGER_KEY));
        }
        
        $logger = null;
        if ($errorHandlerConfig['loggerEnabled']) {
            /** @var LoggerInterface $logger */
            $logger = $container->get($errorHandlerConfig['logger']);
        }

        $generator = $container->has(\Mezzio\Middleware\ErrorResponseGenerator::class)
            ? $container->get(\Mezzio\Middleware\ErrorResponseGenerator::class)
            : null;

        return new LogErrorHandler($container->get(ResponseInterface::class), $generator, $logger);
    }
}
