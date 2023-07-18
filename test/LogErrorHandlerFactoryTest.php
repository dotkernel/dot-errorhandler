<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\LogErrorHandler;
use Dot\ErrorHandler\LogErrorHandlerFactory;
use Mezzio\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

class LogErrorHandlerFactoryTest extends TestCase
{
    private ContainerInterface|MockObject $container;
    /** @var callable $responseFactory */
    private $responseFactory;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->container       = $this->createMock(ContainerInterface::class);
        $this->responseFactory = fn(): ResponseInterface => $this->createMock(ResponseInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWithoutConfig(): void
    {
        $this->container->method('get')
            ->with('config')
            ->willReturn(false);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage(
            sprintf('\'[%s\'] not found in config', LogErrorHandlerFactory::ERROR_HANDLER_KEY)
        );

        (new LogErrorHandlerFactory())($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWithMissingLoggerKey(): void
    {
        $this->container->method('get')
            ->with('config')
            ->willReturn([
                LogErrorHandlerFactory::ERROR_HANDLER_KEY => [
                    'loggerEnabled' => true,
                    'test',
                ],
            ]);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage(
            sprintf(
                'Logger: \'[%s\'] is enabled, but not found in config',
                LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY
            )
        );

        (new LogErrorHandlerFactory())($this->container);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillCreateWithValidConfigAndMissingLogger(): void
    {
        $this->container->method('get')
            ->willReturnMap([
                [
                    'config',
                    [
                        LogErrorHandlerFactory::ERROR_HANDLER_KEY => [
                            'loggerEnabled'                                  => true,
                            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => 'test',
                        ],
                    ],
                ],
                [
                    'config[' . LogErrorHandlerFactory::ERROR_HANDLER_KEY
                    . '][' . LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY . ']',
                    null,
                ],
                [ErrorResponseGenerator::class, $this->createMock(ErrorResponseGenerator::class)],
                [ResponseInterface::class, $this->responseFactory],
            ]);

        $result = (new LogErrorHandlerFactory())($this->container);
        $this->assertInstanceOf(LogErrorHandler::class, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testWillCreateWithValidConfig(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $this->container->method('has')
            ->with(ErrorResponseGenerator::class)
            ->willReturn(true);

        $this->container->method('get')
            ->willReturnMap([
                [
                    'config',
                    [
                        LogErrorHandlerFactory::ERROR_HANDLER_KEY => [
                            'loggerEnabled'                                  => true,
                            LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY => 'test',
                        ],
                    ],
                ],
                [
                    'config[' . LogErrorHandlerFactory::ERROR_HANDLER_KEY . ']['
                    . LogErrorHandlerFactory::ERROR_HANDLER_LOGGER_KEY . ']',
                    $logger,
                ],
                [ErrorResponseGenerator::class, $this->createMock(ErrorResponseGenerator::class)],
                [ResponseInterface::class, $this->responseFactory],
            ]);

        $result = (new LogErrorHandlerFactory())($this->container);
        $this->assertInstanceOf(LogErrorHandler::class, $result);
    }
}
