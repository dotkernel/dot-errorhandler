<?php

declare(strict_types=1);

namespace DotTest\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use Dot\ErrorHandler\Extra\Processor\SessionProcessor;
use Dot\ErrorHandler\Extra\Provider\CookieProvider;
use Dot\ErrorHandler\Extra\Provider\HeaderProvider;
use Dot\ErrorHandler\Extra\Provider\RequestProvider;
use Dot\ErrorHandler\Extra\Provider\ServerProvider;
use Dot\ErrorHandler\Extra\Provider\SessionProvider;
use Dot\ErrorHandler\Extra\Provider\TraceProvider;
use Dot\ErrorHandler\LogErrorHandler as Subject;
use Dot\Log\Formatter\Json;
use Dot\Log\Logger;
use DotTest\ErrorHandler\Fixture\TestLogger;
use DotTest\ErrorHandler\Fixture\ThrowingHandler;
use DotTest\ErrorHandler\Fixture\ThrowingLogger;
use DotTest\ErrorHandler\Fixture\ThrowingProcessor;
use ErrorException;
use Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use Laminas\Stdlib\ArrayObject;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

use function error_reporting;
use function file_get_contents;
use function ini_set;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class LogErrorHandlerTest extends TestCase
{
    private Subject $subject;
    private ServerRequestInterface $serverRequest;
    private ResponseInterface $response;
    /** @var callable(): ResponseInterface */
    private $responseFactory;
    private Throwable $exception;
    private ErrorResponseGenerator $errorResponseGenerator;
    private TestLogger $logger;
    private vfsStreamDirectory $fileSystem;

    public function setUp(): void
    {
        $this->response               = new Response();
        $this->serverRequest          = new ServerRequest();
        $this->responseFactory        = fn (): ResponseInterface => $this->response;
        $this->errorResponseGenerator = new ErrorResponseGenerator();
        $this->logger                 = new TestLogger();
        $this->subject                = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger
        );
        $this->exception              = new RuntimeException('Not Implemented', 501);
        $this->fileSystem             = vfsStream::setup('root', 0644, ['log']);
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

        $callableErrorHandler(error_reporting(), ErrorException::class, 'testErrorFile', 0);
    }

    public function testCreateErrorHandlerSkipsErrorsOutsideErrorReportingMask(): void
    {
        $callableErrorHandler = $this->subject->createErrorHandler();
        $this->assertNull($callableErrorHandler(-(error_reporting() + 1), ErrorException::class, 'testErrorFile', 0));
    }

    public function testLogErrorHandlerWillInitiateWhenExtraProviderIsMissing(): void
    {
        $logErrorHandler = new Subject($this->responseFactory, $this->errorResponseGenerator, $this->logger);

        $this->assertNull($logErrorHandler->getExtraProvider());
    }

    public function testLogErrorHandlerWillInitiateWhenExtraProviderIsNull(): void
    {
        $logErrorHandler = new Subject($this->responseFactory, $this->errorResponseGenerator, $this->logger, null);

        $this->assertNull($logErrorHandler->getExtraProvider());
    }

    public function testLogErrorHandlerWillInitiateWhenExtraProviderIsProvided(): void
    {
        $logErrorHandler = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger,
            new ExtraProvider()
        );

        $this->assertInstanceOf(ExtraProvider::class, $logErrorHandler->getExtraProvider());
    }

    public function testLogErrorHandlerLogsWillOnlyContainDefaultKeysWhenNoProvidersAreEnabled(): void
    {
        $logErrorHandler = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger,
            new ExtraProvider()
        );

        $log = $logErrorHandler->provideExtra(new Exception('test'), $this->serverRequest);
        $this->assertCount(2, $log);
        $this->assertArrayHasKey('file', $log);
        $this->assertArrayHasKey('line', $log);
    }

    public function testLogErrorHandlerLogsWillContainExtraKeysWhenProvidersAreEnabled(): void
    {
        $logErrorHandler = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger,
            new ExtraProvider([
                CookieProvider::class  => ['enabled' => true],
                HeaderProvider::class  => ['enabled' => true],
                RequestProvider::class => ['enabled' => true],
                ServerProvider::class  => ['enabled' => true],
                SessionProvider::class => ['enabled' => true],
                TraceProvider::class   => ['enabled' => true],
            ])
        );

        $extra = $logErrorHandler->provideExtra(new Exception('test'), $this->serverRequest);

        $this->assertCount(8, $extra);
        $this->assertArrayHasKey('file', $extra);
        $this->assertArrayHasKey('line', $extra);
        $this->assertArrayHasKey('cookie', $extra);
        $this->assertArrayHasKey('header', $extra);
        $this->assertArrayHasKey('request', $extra);
        $this->assertArrayHasKey('server', $extra);
        $this->assertArrayHasKey('session', $extra);
        $this->assertArrayHasKey('trace', $extra);
    }

    /**
     * F16: provideExtra() reads the session from $_SESSION.
     */
    public function testLogErrorHandlerLogsSessionData(): void
    {
        $logErrorHandler = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger,
            new ExtraProvider([
                SessionProvider::class => ['enabled' => true, 'processor' => ['class' => SessionProcessor::class]],
            ])
        );

        $hadSession = isset($_SESSION);
        $previous   = $_SESSION ?? [];
        $_SESSION   = ['user_id' => 5, 'container' => new ArrayObject(['token' => 'abc'])];

        try {
            $extra = $logErrorHandler->provideExtra(new Exception('test'), $this->serverRequest);
        } finally {
            if ($hadSession) {
                $_SESSION = $previous;
            } else {
                unset($_SESSION);
            }
        }

        $this->assertSame(['user_id' => 5, 'container' => ['token' => 'abc']], $extra['session']);
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

    public function testTriggerListenersWithTwoListeners(): void
    {
        $calls     = 0;
        $listener1 = function (
            Throwable $error,
            ServerRequestInterface $request,
            ResponseInterface $response
        ) use (&$calls): void {
            $calls++;
            $this->assertSame($this->exception, $error);
            $this->assertSame($this->serverRequest, $request);
            $this->assertSame($this->response, $response);
        };

        $listener2 = clone $listener1;
        $this->subject->attachListener($listener1);
        $this->subject->attachListener($listener2);

        $this->subject->triggerListeners($this->exception, $this->serverRequest, $this->response);

        $this->assertSame(2, $calls);
    }

    public function testHandleThrowable(): void
    {
        $response = $this->subject->handleThrowable($this->exception, $this->serverRequest);

        $this->assertSame(501, $response->getStatusCode());
        $this->assertSame('Not Implemented', (string) $response->getBody());
        $this->assertCount(1, $this->logger->records);
        $this->assertSame('Not Implemented', $this->logger->records[0]['message']);
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
            $this->assertSame($this->exception, $error, 'Listener did not receive same exception as was raised');
            $this->assertSame($this->serverRequest, $request, 'Listener did not receive same request');
            $this->assertSame(501, $response->getStatusCode(), 'Listener did not receive the error response');
        };

        $listener2 = clone $listener;
        $this->subject->attachListener($listener);
        $this->subject->attachListener($listener2);

        $result = $this->subject->process($this->serverRequest, new ThrowingHandler($this->exception));

        $this->assertSame(501, $result->getStatusCode());
        $this->assertSame(2, $calls);
    }

    /**
     * F1: a failing logger must not replace the error response with its own exception.
     */
    public function testWillReturnErrorResponseWhenLoggerThrows(): void
    {
        $calls   = 0;
        $subject = new Subject($this->responseFactory, $this->errorResponseGenerator, new ThrowingLogger());
        $subject->attachListener(static function () use (&$calls): void {
            $calls++;
        });

        [$response, $errorLog] = $this->captureErrorLog(
            fn (): ResponseInterface => $subject->process($this->serverRequest, new ThrowingHandler($this->exception))
        );

        $this->assertSame(501, $response->getStatusCode());
        $this->assertSame(1, $calls, 'Listeners must still be triggered');
        $this->assertStringContainsString('RuntimeException: Not Implemented', $errorLog);
        $this->assertStringContainsString('logging failed: disk full', $errorLog);
    }

    /**
     * F1: a failing extra-data processor must not replace the error response either.
     */
    public function testWillReturnErrorResponseWhenExtraProcessingThrows(): void
    {
        $subject = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            $this->logger,
            new ExtraProvider([
                CookieProvider::class => ['enabled' => true, 'processor' => ['class' => ThrowingProcessor::class]],
            ])
        );

        [$response, $errorLog] = $this->captureErrorLog(
            fn (): ResponseInterface => $subject->process($this->serverRequest, new ThrowingHandler($this->exception))
        );

        $this->assertSame(501, $response->getStatusCode());
        $this->assertCount(0, $this->logger->records);
        $this->assertStringContainsString('RuntimeException: Not Implemented', $errorLog);
        $this->assertStringContainsString('logging failed: processor failure', $errorLog);
    }

    /**
     * Runs $action with PHP's error_log() redirected to a temporary file.
     *
     * @param callable(): ResponseInterface $action
     * @return array{ResponseInterface, string} the response, and what was written to the error log
     */
    private function captureErrorLog(callable $action): array
    {
        $file     = (string) tempnam(sys_get_temp_dir(), 'dot-errorhandler-');
        $previous = ini_set('error_log', $file);

        try {
            $response = $action();

            return [$response, (string) file_get_contents($file)];
        } finally {
            ini_set('error_log', (string) $previous);
            unlink($file);
        }
    }

    /**
     * Integration check of the documented setup: dot-log's Logger writing JSON to a file.
     *
     * @throws ContainerExceptionInterface
     */
    public function testHandleThrowableLogsError(): void
    {
        $logErrorHandler = new Subject(
            $this->responseFactory,
            $this->errorResponseGenerator,
            new Logger($this->getConfig())
        );

        $logErrorHandler->process($this->serverRequest, new ThrowingHandler($this->exception));

        $this->assertTrue($this->fileSystem->hasChild('test-error-log.log'));
        $this->assertNotEmpty(file_get_contents($this->fileSystem->url() . '/test-error-log.log'));
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfig(): array
    {
        return [
            'writers' => [
                'FileWriter' => [
                    'name'    => 'stream',
                    'level'   => Logger::ALERT,
                    'options' => [
                        'stream'    => $this->fileSystem->url() . '/test-error-log.log',
                        'filters'   => [
                            'allMessages' => [
                                'name'    => 'level',
                                'options' => [
                                    'operator' => '>=',
                                    'level'    => Logger::EMERG,
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
