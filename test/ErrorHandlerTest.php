<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\ErrorHandler as Subject;
use DotTest\ErrorHandler\Fixture\ResponseHandler;
use DotTest\ErrorHandler\Fixture\ThrowingHandler;
use ErrorException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class ErrorHandlerTest extends TestCase
{
    private Subject $subject;
    private ServerRequestInterface $serverRequest;
    private ResponseInterface $response;
    private Throwable $exception;

    public function setUp(): void
    {
        $this->response      = new Response();
        $this->serverRequest = new ServerRequest();
        $this->exception     = new RuntimeException('Not Implemented', 501);
        $this->subject       = new Subject(fn (): ResponseInterface => $this->response, new ErrorResponseGenerator());
    }

    public function testWillCreateWithDefaultParameters(): void
    {
        $this->assertContainsOnlyInstancesOf(Subject::class, [$this->subject]);
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
        $calls    = 0;
        $listener = static function () use (&$calls): void {
            $calls++;
        };

        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener);
        $this->subject->triggerListeners($this->exception, $this->serverRequest, $this->response);

        $this->assertSame(1, $calls);
    }

    public function testHandleThrowable(): void
    {
        $response = $this->subject->handleThrowable($this->exception, $this->serverRequest);

        $this->assertSame(501, $response->getStatusCode());
        $this->assertSame('Not Implemented', (string) $response->getBody());
    }

    public function testErrorHandlingTriggersListeners(): void
    {
        $calls    = 0;
        $listener = function (
            Throwable $error,
            ServerRequestInterface $request,
            ResponseInterface $response
        ) use (&$calls): void {
            $calls++;
            $this->assertSame($this->exception, $error);
            $this->assertSame($this->serverRequest, $request);
            $this->assertSame(501, $response->getStatusCode());
        };

        $listener2 = clone $listener;

        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener2);

        $result = $this->subject->process($this->serverRequest, new ThrowingHandler($this->exception));

        $this->assertSame(501, $result->getStatusCode());
        $this->assertSame(2, $calls);
    }

    public function testProcessReturnsHandlerResponseAndRestoresErrorHandler(): void
    {
        $sentinel = static fn (): bool => false;
        set_error_handler($sentinel);

        try {
            $expected = new Response(status: 204);
            $result   = $this->subject->process($this->serverRequest, new ResponseHandler($expected));

            $this->assertSame($expected, $result);
            $this->assertSame($sentinel, $this->currentErrorHandler());
        } finally {
            restore_error_handler();
        }
    }

    public function testProcessRestoresErrorHandlerWhenErrorHandlingThrows(): void
    {
        $sentinel = static fn (): bool => false;
        set_error_handler($sentinel);

        try {
            $this->subject->attachListener(static function (): void {
                throw new RuntimeException('listener failure');
            });

            try {
                $this->subject->process($this->serverRequest, new ThrowingHandler($this->exception));
                $this->fail('Expected the listener exception to propagate');
            } catch (RuntimeException $e) {
                $this->assertSame('listener failure', $e->getMessage());
            }

            $this->assertSame($sentinel, $this->currentErrorHandler());
        } finally {
            restore_error_handler();
        }
    }

    public function testWillUseDefaultResponseGeneratorWhenNoneIsProvided(): void
    {
        $subject  = new Subject(static fn (): ResponseInterface => new Response());
        $response = $subject->handleThrowable($this->exception, $this->serverRequest);

        $this->assertSame(501, $response->getStatusCode());
    }

    private function currentErrorHandler(): ?callable
    {
        $current = set_error_handler(null);
        restore_error_handler();

        return $current;
    }
}
