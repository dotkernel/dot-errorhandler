<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use Dot\ErrorHandler\Extra\Provider\TraceProvider;
use Dot\ErrorHandler\LogErrorHandler;
use Dot\ErrorHandler\LogErrorHandlerFactory;
use DotTest\ErrorHandler\Fixture\InMemoryContainer;
use DotTest\ErrorHandler\Fixture\TestLogger;
use DotTest\ErrorHandler\Fixture\ThrowingHandler;
use InvalidArgumentException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use stdClass;

use function sprintf;

class LogErrorHandlerFactoryTest extends TestCase
{
    private const LOGGER_SERVICE = 'test.logger';

    private TestLogger $logger;

    public function setUp(): void
    {
        $this->logger = new TestLogger();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWithoutConfig(): void
    {
        $container = new InMemoryContainer(['config' => []]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('\'[%s\'] not found in config', LogErrorHandlerFactory::ERROR_HANDLER_KEY)
        );

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWithMissingLoggerKey(): void
    {
        $container = $this->createContainer([
            'loggerEnabled' => true,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Logger: \'[%s\'] is enabled, but not found in config',
                LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY
            )
        );

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenLoggerServiceIsMissing(): void
    {
        $container = new InMemoryContainer([
            'config'                 => [
                LogErrorHandlerFactory::ERROR_HANDLER_KEY => [
                    'loggerEnabled'                                  => true,
                    LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => 'missing.logger',
                ],
            ],
            ResponseInterface::class => static fn (): ResponseInterface => new Response(),
        ]);

        $this->expectException(NotFoundExceptionInterface::class);

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * F10: a missing loggerEnabled key means "disabled", without a PHP warning.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithoutLoggerWhenLoggerEnabledIsMissing(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer([]));

        $this->assertLoggerIsNotInjected($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithoutLoggerWhenLoggerIsDisabled(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer([
            'loggerEnabled'                                  => false,
            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => self::LOGGER_SERVICE,
        ]));

        $this->assertLoggerIsNotInjected($result);
    }

    /**
     * F10: a non-boolean flag (for example from an environment variable) must not silently disable logging.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenLoggerEnabledIsNotBoolean(): void
    {
        $container = $this->createContainer([
            'loggerEnabled'                                  => 'true',
            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => self::LOGGER_SERVICE,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a boolean');

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * F10
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenLoggerKeyIsNotAString(): void
    {
        $container = $this->createContainer([
            'loggerEnabled'                                  => true,
            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => 123,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Logger: \'[%s\'] is enabled, but not found in config',
                LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY
            )
        );

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * F10
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenLoggerServiceIsNotALogger(): void
    {
        $container = $this->createContainer($this->loggerConfig(), [self::LOGGER_SERVICE => new stdClass()]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Service "%s" must implement %s', self::LOGGER_SERVICE, LoggerInterface::class)
        );

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * F10
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenResponseFactoryIsNotCallable(): void
    {
        $container = $this->createContainer($this->loggerConfig(), [ResponseInterface::class => 'not callable']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Service "%s" must be a callable response factory', ResponseInterface::class)
        );

        (new LogErrorHandlerFactory())($container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithoutExtraProviderConfig(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer($this->loggerConfig()));

        $this->assertNull($result->getExtraProvider());
        $this->assertLoggerIsInjected($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithEmptyExtraProviderConfig(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer(
            $this->loggerConfig() + [ExtraProvider::CONFIG_KEY => []]
        ));

        $this->assertNull($result->getExtraProvider());
        $this->assertLoggerIsInjected($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithInvalidExtraProviderConfig(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer(
            $this->loggerConfig() + [ExtraProvider::CONFIG_KEY => ['test' => 'test']]
        ));

        $extraProvider = $result->getExtraProvider();
        $this->assertInstanceOf(ExtraProvider::class, $extraProvider);
        $this->assertFalse($extraProvider->getTrace()->isEnabled());
        $this->assertLoggerIsInjected($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithValidConfig(): void
    {
        $result = (new LogErrorHandlerFactory())($this->createContainer(
            $this->loggerConfig() + [ExtraProvider::CONFIG_KEY => [TraceProvider::class => ['enabled' => true]]],
            [ErrorResponseGenerator::class => new ErrorResponseGenerator()]
        ));

        $extraProvider = $result->getExtraProvider();
        $this->assertInstanceOf(ExtraProvider::class, $extraProvider);
        $this->assertTrue($extraProvider->getTrace()->isEnabled());
        $this->assertLoggerIsInjected($result);
        $this->assertArrayHasKey('trace', $this->logger->records[0]['context']);
    }

    /**
     * @param array<string, mixed> $errorHandlerConfig
     * @param array<string, mixed> $services
     */
    private function createContainer(array $errorHandlerConfig, array $services = []): InMemoryContainer
    {
        return new InMemoryContainer($services + [
            'config'                 => [
                LogErrorHandlerFactory::ERROR_HANDLER_KEY => $errorHandlerConfig,
            ],
            self::LOGGER_SERVICE     => $this->logger,
            ResponseInterface::class => static fn (): ResponseInterface => new Response(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function loggerConfig(): array
    {
        return [
            'loggerEnabled'                                  => true,
            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => self::LOGGER_SERVICE,
        ];
    }

    private function assertLoggerIsNotInjected(LogErrorHandler $handler): void
    {
        $response = $handler->process(new ServerRequest(), new ThrowingHandler(new RuntimeException('boom')));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertCount(0, $this->logger->records);
    }

    private function assertLoggerIsInjected(LogErrorHandler $handler): void
    {
        $response = $handler->process(new ServerRequest(), new ThrowingHandler(new RuntimeException('boom')));

        $this->assertSame(500, $response->getStatusCode());
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('boom', $this->logger->records[0]['message']);
    }
}
