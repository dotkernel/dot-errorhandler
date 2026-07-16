<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use ErrorException;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function error_reporting;
use function in_array;
use function restore_error_handler;
use function set_error_handler;

class LogErrorHandler implements MiddlewareInterface, ErrorHandlerInterface
{
    /** @var callable[] */
    private $listeners = [];
    /** @var callable|null Routine that will generate the error response. */
    private $responseGenerator;
    /** @var callable */
    private $responseFactory;
    private ?LoggerInterface $logger;
    private ?ExtraProvider $extraProvider;

    /**
     * @param callable $responseFactory A factory capable of returning an
     *     empty ResponseInterface instance to update and return when returning
     *     an error response.
     * @param null|callable $responseGenerator Callback that will generate the final
     *     error response; if none is provided, ErrorResponseGenerator is used.
     */
    public function __construct(
        callable $responseFactory,
        ?callable $responseGenerator = null,
        ?LoggerInterface $logger = null,
        ?ExtraProvider $extraProvider = null,
    ) {
        $this->responseFactory   = function () use ($responseFactory): ResponseInterface {
            return $responseFactory();
        };
        $this->responseGenerator = $responseGenerator ?: new ErrorResponseGenerator();
        $this->logger            = $logger;
        $this->extraProvider     = $extraProvider;
    }

    public function attachListener(callable $listener): void
    {
        if (in_array($listener, $this->listeners, true)) {
            return;
        }

        $this->listeners[] = $listener;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        set_error_handler($this->createErrorHandler());

        try {
            $response = $handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->handleThrowable($e, $request);
        }

        restore_error_handler();

        return $response;
    }

    /**
     * Handles all throwables, generating and returning a response.
     *
     * Passes the error, request, and response prototype to createErrorResponse(),
     * triggers all listeners with the same arguments (but using the response
     * returned from createErrorResponse()), and then returns the response.
     *
     * If a valid Logger is available, the error and its message are logged in the
     * configured format.
     */
    public function handleThrowable(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $generator = $this->responseGenerator;
        if ($this->logger instanceof LoggerInterface) {
            $extra = $this->provideExtra($e, $request);
            $this->logger->error($e->getMessage(), $extra);
        }

        $response = $generator($e, $request, ($this->responseFactory)());
        $this->triggerListeners($e, $request, $response);

        return $response;
    }

    /**
     * Creates and returns a callable error handler that raises exceptions.
     *
     * Only raises exceptions for errors that are within the error_reporting mask.
     */
    public function createErrorHandler(): callable
    {
        /**
         * @throws ErrorException if error is not within the error_reporting mask.
         */
        return function (int $errno, string $errstr, string $errfile, int $errline): void {
            if (! (error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }

    /**
     * Trigger all error listeners.
     */
    public function triggerListeners(
        Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): void {
        foreach ($this->listeners as $listener) {
            $listener($error, $request, $response);
        }
    }

    public function provideExtra(Throwable $throwable, ServerRequestInterface $request): array
    {
        $extra = [
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
        ];

        if ($this->extraProvider?->getCookie()->isEnabled()) {
            $extra['cookie'] = $this->extraProvider->getCookie()->provide($request->getCookieParams());
        }

        if ($this->extraProvider?->getHeader()->isEnabled()) {
            $extra['header'] = $this->extraProvider->getHeader()->provide($request->getHeaders());
        }

        if ($this->extraProvider?->getRequest()->isEnabled()) {
            $extra['request'] = $this->extraProvider->getRequest()->provide((array) $request->getParsedBody());
        }

        if ($this->extraProvider?->getServer()->isEnabled()) {
            $extra['server'] = $this->extraProvider->getServer()->provide($request->getServerParams());
        }

        if ($this->extraProvider?->getSession()->isEnabled()) {
            $extra['session'] = $this->extraProvider->getSession()->provide($_SESSION ?? []);
        }

        if ($this->extraProvider?->getTrace()->isEnabled()) {
            $extra['trace'] = $this->extraProvider->getTrace()->provide($throwable->getTrace());
        }

        return $extra;
    }

    public function getExtraProvider(): ?ExtraProvider
    {
        return $this->extraProvider;
    }
}
