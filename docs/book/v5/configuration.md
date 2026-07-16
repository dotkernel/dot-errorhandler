# Configuration

Register `dot-errorhandler` in your project by adding `Dot\ErrorHandler\ConfigProvider::class` to your configuration aggregator (to `config/config.php` for example), and add `\Dot\ErrorHandler\ErrorHandlerInterface::class` (to `config/pipeline.php` for example) **as the outermost layer of the middleware** to catch all exceptions.

Configure the error handler as shown below.

In **config/autoload/error-handling.global.php**:

```php
<?php

declare(strict_types=1);

use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;

return [
    'dependencies'     => [
        'aliases' => [
            ErrorHandlerInterface::class => LogErrorHandler::class,
        ],
    ],
    'dot-errorhandler' => [
        'loggerEnabled' => true,
        'logger'        => 'dot-log.default_logger',
    ]
];
```

A configuration example for the default logger can be found in `config/log.global.php.dist`.

When configuring the error handler in your application, you can choose between two classes:

- `Dot\ErrorHandler\LogErrorHandler`: for logging and displaying errors
- `Dot\ErrorHandler\ErrorHandler`: for displaying errors only

> Both `LogErrorHandler` and `ErrorHandler` have factories declared in the package's `ConfigProvider`.
> If you need a custom ErrorHandler, it must have a factory declared in the config, as in the below example:

```php
<?php

declare(strict_types=1);

return [
    'dependencies'     => [
        'factories' => [
            \App\CustomErrorHandler::class => \App\CustomHandlerFactory::class,
        ],
        'aliases' => [
            \Dot\ErrorHandler\ErrorHandlerInterface::class => \App\CustomErrorHandler::class,
        ],
    ],
    'dot-errorhandler' => [
        'loggerEnabled' => true,
        'logger'        => 'dot-log.default_logger',
    ],
];
```

Config examples can be found in this project's `config` directory.
