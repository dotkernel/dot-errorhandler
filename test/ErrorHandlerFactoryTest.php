<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\ErrorHandler;
use Dot\ErrorHandler\ErrorHandlerFactory;
use DotTest\ErrorHandler\Fixture\InMemoryContainer;
use DotTest\ErrorHandler\Fixture\ThrowingHandler;
use InvalidArgumentException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

use function sprintf;

class ErrorHandlerFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithDefaultOption(): void
    {
        $container = new InMemoryContainer([
            ResponseInterface::class => static fn (): ResponseInterface => new Response(),
        ]);

        $result = (new ErrorHandlerFactory())($container);

        $this->assertSame(ErrorHandler::class, $result::class);
        $response = $result->process(new ServerRequest(), new ThrowingHandler(new RuntimeException('boom')));
        $this->assertSame(500, $response->getStatusCode());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateWithErrorResponseGenerator(): void
    {
        $container = new InMemoryContainer([
            ErrorResponseGenerator::class => new ErrorResponseGenerator(),
            ResponseInterface::class      => static fn (): ResponseInterface => new Response(),
        ]);

        $result = (new ErrorHandlerFactory())($container);

        $this->assertSame(ErrorHandler::class, $result::class);
        $response = $result->process(new ServerRequest(), new ThrowingHandler(new RuntimeException('boom')));
        $this->assertSame(500, $response->getStatusCode());
    }

    /**
     * F17: any callable generator is accepted, whatever zend.assertions is set to.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillUseAnyCallableErrorResponseGenerator(): void
    {
        $container = new InMemoryContainer([
            ErrorResponseGenerator::class => static fn (
                Throwable $e,
                ServerRequestInterface $request,
                ResponseInterface $response
            ): ResponseInterface => $response->withStatus(418),
            ResponseInterface::class      => static fn (): ResponseInterface => new Response(),
        ]);

        $result   = (new ErrorHandlerFactory())($container);
        $response = $result->process(new ServerRequest(), new ThrowingHandler(new RuntimeException('boom')));

        $this->assertSame(418, $response->getStatusCode());
    }

    /**
     * F17: validated with an exception, not with assert(), which is compiled out in production.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenResponseFactoryIsNotCallable(): void
    {
        $container = new InMemoryContainer([ResponseInterface::class => 'not callable']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf('Service "%s" must be a callable response factory', ResponseInterface::class)
        );

        (new ErrorHandlerFactory())($container);
    }

    /**
     * F17
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateWhenErrorResponseGeneratorIsNotCallable(): void
    {
        $container = new InMemoryContainer([
            ErrorResponseGenerator::class => 'not callable',
            ResponseInterface::class      => static fn (): ResponseInterface => new Response(),
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Service "%s" must be callable', ErrorResponseGenerator::class));

        (new ErrorHandlerFactory())($container);
    }
}
