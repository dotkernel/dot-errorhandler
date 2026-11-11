<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Dot\ErrorHandler\Extra\ExtraProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

use function error_log;
use function sprintf;

class LogErrorHandler extends ErrorHandler
{
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
        private readonly ?LoggerInterface $logger = null,
        private readonly ?ExtraProvider $extraProvider = null,
    ) {
        parent::__construct($responseFactory, $responseGenerator);
    }

    /**
     * Handles all throwables, generating and returning a response.
     *
     * If a valid Logger is available, the error and its message are logged in the
     * configured format before the error response is generated and the listeners
     * are triggered. Logging never prevents the error response: if the logger or an
     * extra-data processor fails, the original error is written to PHP's error_log().
     */
    public function handleThrowable(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $this->log($e, $request);

        return parent::handleThrowable($e, $request);
    }

    private function log(Throwable $e, ServerRequestInterface $request): void
    {
        if (! $this->logger instanceof LoggerInterface) {
            return;
        }

        try {
            $this->logger->error($e->getMessage(), $this->provideExtra($e, $request));
        } catch (Throwable $failure) {
            error_log(sprintf(
                '%s: %s in %s:%d (logging failed: %s)',
                $e::class,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine(),
                $failure->getMessage()
            ));
        }
    }

    /**
     * @return array<string, mixed>
     */
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
