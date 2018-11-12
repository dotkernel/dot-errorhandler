<?php
/**
 * Created by PhpStorm.
 * User: gabidjdev
 * Date: 12/11/2018
 * Time: 19:22
 */

declare(strict_types=1);

namespace Dot\ErrorHandler;

use Zend\Expressive\Container\ErrorHandlerFactory;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'aliases' => [
                    ErrorHandlerInterface::class => ErrorHandler::class,
                ],
                'factories' => [
                    LogErrorHandler::class => LogErrorHandlerFactory::class,
                    ErrorHandler::class => ErrorHandlerFactory::class,
                ]
            ],
        ];
    }
}
