<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use InvalidArgumentException;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function count;
use function is_array;
use function is_bool;
use function is_callable;
use function is_string;
use function sprintf;

class LogErrorHandlerFactory
{
    public const ERROR_HANDLER_KEY        = 'dot-errorhandler';
    public const ERROR_HANDLER_LOGGER_KEY = 'logger';

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException When the configuration or a configured service is invalid.
     */
    public function __invoke(ContainerInterface $container): LogErrorHandler
    {
        $errorHandlerConfig = $container->get('config')[self::ERROR_HANDLER_KEY] ?? null;
        if (! is_array($errorHandlerConfig)) {
            throw new InvalidArgumentException(sprintf('\'[%s\'] not found in config', self::ERROR_HANDLER_KEY));
        }

        $responseFactory = $container->get(ResponseInterface::class);
        if (! is_callable($responseFactory)) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" must be a callable response factory', ResponseInterface::class)
            );
        }

        $generator = $container->has(ErrorResponseGenerator::class)
            ? $container->get(ErrorResponseGenerator::class)
            : null;
        if ($generator !== null && ! is_callable($generator)) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" must be callable', ErrorResponseGenerator::class)
            );
        }

        $extraProvider = null;
        $extraConfig   = $errorHandlerConfig[ExtraProvider::CONFIG_KEY] ?? null;
        if (is_array($extraConfig) && count($extraConfig) > 0) {
            $extraProvider = new ExtraProvider($extraConfig);
        }

        return new LogErrorHandler(
            $responseFactory,
            $generator,
            $this->createLogger($container, $errorHandlerConfig),
            $extraProvider
        );
    }

    /**
     * @param array<array-key, mixed> $errorHandlerConfig
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private function createLogger(ContainerInterface $container, array $errorHandlerConfig): ?LoggerInterface
    {
        $loggerEnabled = $errorHandlerConfig['loggerEnabled'] ?? false;
        if (! is_bool($loggerEnabled)) {
            throw new InvalidArgumentException('Logger: \'[loggerEnabled\'] must be a boolean');
        }

        if (! $loggerEnabled) {
            return null;
        }

        $loggerService = $errorHandlerConfig[self::ERROR_HANDLER_LOGGER_KEY] ?? null;
        if (! is_string($loggerService)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Logger: \'[%s\'] is enabled, but not found in config',
                    self::ERROR_HANDLER_LOGGER_KEY
                )
            );
        }

        $logger = $container->get($loggerService);
        if (! $logger instanceof LoggerInterface) {
            throw new InvalidArgumentException(
                sprintf('Service "%s" must implement %s', $loggerService, LoggerInterface::class)
            );
        }

        return $logger;
    }
}
