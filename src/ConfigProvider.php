<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'aliases'   => [
                    ErrorHandlerInterface::class => ErrorHandler::class,
                ],
                'factories' => [
                    LogErrorHandler::class => LogErrorHandlerFactory::class,
                    ErrorHandler::class    => ErrorHandlerFactory::class,
                ],
            ],
        ];
    }
}
