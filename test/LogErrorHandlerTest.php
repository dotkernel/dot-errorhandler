<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\LogErrorHandler;
use Dot\ErrorHandler\LogErrorHandler as Subject;
use ErrorException;
use Laminas\Log\Formatter\Json;
use Laminas\Log\Logger;
use Laminas\Log\LoggerInterface;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
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
use function file_get_contents;

class LogErrorHandlerTest extends TestCase
{
    private Subject $subject;
    private ServerRequestInterface|MockObject $serverRequest;
    private ResponseInterface|MockObject $response;
    /** @var callable():ResponseInterface $responseFactory */
    private $responseFactory;
    private Throwable|MockObject $exception;
    private ErrorResponseGenerator $errorResponseGenerator;
    private vfsStreamDirectory $fileSystem;

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
        $this->subject                = new LogErrorHandler(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->createMock(LoggerInterface::class)
        );
        $this->exception              = new RuntimeException('Not Implemented', 501);
        $this->fileSystem             = vfsStream::setup('root', 0644, ['log']);
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

        $callableErrorHandler(error_reporting(), ErrorException::class, 'testErrfile', 0);
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

    public function testTriggerListenersWithTwoListeners(): void
    {
        $listener1 = function (
            Throwable $error,
            ServerRequestInterface $request,
            ResponseInterface $response
        ): void {
            $this->assertSame($this->exception, $error);
            $this->assertSame($this->serverRequest, $request);
            $this->assertSame($this->response, $response);
        };

        $listener2 = clone $listener1;
        $this->subject->attachListener($listener1);
        $this->subject->attachListener($listener2);

        $this->subject->triggerListeners($this->exception, $this->serverRequest, $this->response);
    }

    public function testHandleThrowable(): void
    {
        $responseGenerator = new ErrorResponseGenerator();

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

        $response = $responseGenerator($this->exception, $this->serverRequest, ($this->responseFactory)());

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
            $this->assertSame($this->exception, $error, 'Listener did not receive same exception as was raised');
            $this->assertSame($this->serverRequest, $request, 'Listener did not receive same request');
            $this->assertSame($this->response, $response, 'Listener did not receive same response');
        };

        $listener2 = clone $listener;
        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener2);

        $result = $this->subject->process($this->serverRequest, $this->handler);

        $this->assertSame($this->response, $result);
    }

    public function testHandleThrowableLogsError(): void
    {
        $config = $this->getConfig();

        $logErrorHandler = new LogErrorHandler(
            $this->responseFactory,
            $this->errorResponseGenerator,
            new Logger($config)
        );

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

        $logErrorHandler->process($this->serverRequest, $this->handler);

        $this->assertTrue($this->fileSystem->hasChild('test-error-log.log'));
        $this->assertNotEmpty(file_get_contents($this->fileSystem->url() . '/test-error-log.log'));
    }

    private function getConfig(): array
    {
        return [
            'writers' => [
                'FileWriter' => [
                    'name'     => 'stream',
                    'priority' => Logger::ALERT,
                    'options'  => [
                        'stream'    => $this->fileSystem->url() . '/test-error-log.log',
                        'filters'   => [
                            'allMessages' => [
                                'name'    => 'priority',
                                'options' => [
                                    'operator' => '>=',
                                    'priority' => Logger::EMERG,
                                ],
                            ],
                        ],
                        'formatter' => [
                            'name' => Json::class,
                        ],
                    ],
                ],
            ],
        ];
    }
}
