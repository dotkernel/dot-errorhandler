<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\ErrorHandler;
use Dot\ErrorHandler\ErrorHandlerFactory;
use Mezzio\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorHandlerFactoryTest extends TestCase
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
    public function testWillCreateWithDefaultOption(): void
    {
        $this->container->method('has')
            ->with(ErrorResponseGenerator::class)
            ->willReturn(false);

        $this->container->method('get')
            ->with(ResponseInterface::class)
            ->willReturn($this->responseFactory);

        $result = (new ErrorHandlerFactory())($this->container);
        $this->assertInstanceOf(ErrorHandler::class, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithErrorResponseGenerator(): void
    {
        $this->container->method('has')
            ->with(ErrorResponseGenerator::class)
            ->willReturn($this->createMock(ErrorResponseGenerator::class));

        $this->container->method('get')
            ->willReturnMap([
                [ErrorResponseGenerator::class, $this->createMock(ErrorResponseGenerator::class)],
                [ResponseInterface::class, $this->responseFactory],
            ]);

        $result = (new ErrorHandlerFactory())($this->container);
        $this->assertInstanceOf(ErrorHandler::class, $result);
    }
}
