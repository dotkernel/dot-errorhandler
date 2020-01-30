<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

use ErrorException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Laminas\Stratigility\Exception\MissingResponseException;

use function error_reporting;
use function in_array;
use function restore_error_handler;
use function set_error_handler;

/**
 * Error handler middleware.
 *
 * Use this middleware as the outermost (or close to outermost) middleware
 * layer, and use it to intercept PHP errors and exceptions.
 *
 * The class offers two extension points:
 *
 * - Error response generators.
 * - Listeners.
 *
 * Error response generators are callables with the following signature:
 *
 * <code>
 * function (
 *     Throwable $e,
 *     ServerRequestInterface $request,
 *     ResponseInterface $response
 * ) : ResponseInterface
 * </code>
 *
 * These are provided the error, and the request responsible; the response
 * provided is the response prototype provided to the ErrorHandler instance
 * itself, and can be used as the basis for returning an error response.
 *
 * An error response generator must be provided as a constructor argument;
 * if not provided, an instance of Laminas\Stratigility\Middleware\ErrorResponseGenerator
 * will be used.
 *
 * Listeners use the following signature:
 *
 * <code>
 * function (
 *     Throwable $e,
 *     ServerRequestInterface $request,
 *     ResponseInterface $response
 * ) : void
 * </code>
 *
 * Listeners are given the error, the request responsible, and the generated
 * error response, and can then react to them. They are best suited for
 * logging and monitoring purposes.
 *
 * Listeners are attached using the attachListener() method, and triggered
 * in the order attached.
 */
interface ErrorHandlerInterface extends MiddlewareInterface
{
    /**
     * Attach an error listener.
     *
     * Each listener receives the following three arguments:
     *
     * - Throwable $error
     * - ServerRequestInterface $request
     * - ResponseInterface $response
     *
     * These instances are all immutable, and the return values of
     * listeners are ignored; use listeners for reporting purposes
     * only.
     */
    public function attachListener(callable $listener) : void;

    /**
     * Middleware to handle errors and exceptions in layers it wraps.
     *
     * Adds an error handler that will convert PHP errors to ErrorException
     * instances.
     *
     * Internally, wraps the call to $next() in a try/catch block, catching
     * all PHP Throwables.
     *
     * When an exception is caught, an appropriate error response is created
     * and returned instead; otherwise, the response returned by $next is
     * used.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface;
}
