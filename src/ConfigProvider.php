<?php

declare(strict_types=1);

namespace Dot\ErrorHandler;

class ConfigProvider
{
    /**
     * @return array{
     *     dependencies: array{
     *         aliases: array<class-string, class-string>,
     *         factories: array<class-string, class-string>,
     *     },
     * }
     */
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
