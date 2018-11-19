<?php

use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;

return [
    'dependencies' => [
        'aliases' => [
            ErrorHandlerInterface::class => LogErrorHandler::class,
        ]
    ],
    'dot-errorhandler' => [
        'loggerEnabled' => true,
        'logger' => 'dot-log.default_logger'
    ]
];