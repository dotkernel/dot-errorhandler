<?php
/**
 * @see       https://github.com/mezzio/mezzio for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/mezzio/mezzio/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Mezzio\Middleware\ErrorResponseGenerator;
// use Dot\ErrorHandler\ErrorHandler;

class ErrorHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ErrorHandler
    {
        $generator = $container->has(ErrorResponseGenerator::class)
            ? $container->get(ErrorResponseGenerator::class)
            : null;

        return new ErrorHandler($container->get(ResponseInterface::class), $generator);
    }
}
