<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Laminas\Escaper\Escaper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

use function is_int;

final class ErrorResponseGenerator
{
    public function __construct(
        private readonly bool $isDevelopmentMode = false,
    ) {
    }

    /**
     * Create/update the response representing the error.
     */
    public function __invoke(
        Throwable $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $response = $response->withStatus(self::getStatusCode($e, $response));
        $body     = $response->getBody();

        if ($this->isDevelopmentMode) {
            $escaper = new Escaper();
            $body->write($escaper->escapeHtml((string) $e));
            return $response;
        }

        $body->write($response->getReasonPhrase() ?: 'Unknown Error');
        return $response;
    }

    /**
     * Determine status code from an error and/or response.
     *
     * If the error is an exception with a code between 400 and 599, returns
     * the exception code.
     *
     * Otherwise, retrieves the code from the response; if not present, or
     * less than 400 or greater than 599, returns 500; otherwise, returns it.
     */
    public static function getStatusCode(Throwable $error, ResponseInterface $response): int
    {
        $errorCode = $error->getCode();
        if (is_int($errorCode) && $errorCode >= 400 && $errorCode < 600) {
            return $errorCode;
        }

        $status = $response->getStatusCode();
        if ($status < 400 || $status >= 600) {
            $status = 500;
        }
        return $status;
    }
}
