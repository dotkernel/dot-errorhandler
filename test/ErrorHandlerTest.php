<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\ErrorHandler;
use Dot\ErrorHandler\ErrorHandler as Subject;
use ErrorException;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionObject;
use RuntimeException;
use Throwable;

use function error_reporting;

class ErrorHandlerTest extends TestCase
{
    private Subject $subject;
    private ServerRequestInterface|MockObject $serverRequest;
    private ResponseInterface|MockObject $response;
    private ErrorResponseGenerator $errorResponseGenerator;
    /** @var callable():ResponseInterface $responseFactory */
    private $responseFactory;
    /** @var MockObject&StreamInterface */
    private $body;
    /** @var MockObject&RequestHandlerInterface */
    private $handler;
    private Throwable|MockObject $exception;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->response      = $this->createMock(ResponseInterface::class);
        $this->serverRequest = $this->createMock(ServerRequestInterface::class);
        $this->body          = $this->createMock(StreamInterface::class);
        $this->handler       = $this->createMock(RequestHandlerInterface::class);

        $this->responseFactory        = fn(): ResponseInterface => $this->response;
        $this->errorResponseGenerator = new ErrorResponseGenerator();
        $this->subject                = new ErrorHandler($this->responseFactory, $this->errorResponseGenerator);
        $this->exception              = new RuntimeException('Not Implemented', 501);
    }

    public function testWillCreateWithDefaultParameters(): void
    {
        $this->assertInstanceOf(Subject::class, $this->subject);
    }

    public function testCreateErrorHandlerReturnsCallable(): void
    {
        $this->assertIsCallable($this->subject->createErrorHandler());
    }

    public function testCreateErrorHandlerRaisesErrorException(): void
    {
        $callableErrorHandler = $this->subject->createErrorHandler();
        $this->expectException(ErrorException::class);

        $callableErrorHandler(error_reporting(), ErrorException::class, 'testErrFile', 0);
    }

    public function testCreateErrorHandlerSkipsErrorsOutsideErrorReportingMask(): void
    {
        $callableErrorHandler = $this->subject->createErrorHandler();
        $this->assertNull($callableErrorHandler(-(error_reporting() + 1), ErrorException::class, 'testErrfile', 0));
    }

    public function testAttachListenerDoesNotAttachDuplicates(): void
    {
        $listener = static function (): void {
        };

        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener);

        $ref       = new ReflectionObject($this->subject);
        $listeners = $ref->getProperty('listeners');

        $this->assertContains($listener, $listeners->getValue($this->subject));
        $this->assertCount(1, $listeners->getValue($this->subject));
    }

    public function testHandleThrowable(): void
    {
        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('Not Implemented')
            ->willReturn(0);

        $this->response
            ->method('getStatusCode')
            ->willReturn(501);
        $this->response
            ->method('withStatus')
            ->with(501)
            ->willReturnSelf();
        $this->response
            ->method('getBody')
            ->willReturn($this->body);
        $this->response
            ->method('getReasonPhrase')
            ->willReturn('Not Implemented');

        $response = ($this->errorResponseGenerator)($this->exception, $this->serverRequest, ($this->responseFactory)());

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testErrorHandlingTriggersListeners(): void
    {
        $this->handler
            ->method('handle')
            ->with($this->serverRequest)
            ->willThrowException($this->exception);

        $this->body
            ->expects(self::once())
            ->method('write')
            ->with('Not Implemented')
            ->willReturn(0);

        $this->response
            ->method('getStatusCode')
            ->willReturn(501);
        $this->response
            ->method('withStatus')
            ->with(501)
            ->willReturnSelf();
        $this->response
            ->method('getBody')
            ->willReturn($this->body);
        $this->response
            ->method('getReasonPhrase')
            ->willReturn('Not Implemented');

        $listener = function (
            Throwable $error,
            ServerRequestInterface $request,
            ResponseInterface $response
        ): void {
            $this->assertSame($this->exception, $error);
            $this->assertSame($this->serverRequest, $request);
            $this->assertSame($this->response, $response);
        };

        $listener2 = clone $listener;

        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener2);

        $result = $this->subject->process($this->serverRequest, $this->handler);

        $this->assertSame($this->response, $result);
    }
}
