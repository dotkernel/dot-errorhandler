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
    ],
];
```

The `dot-errorhandler` keys are:

- **loggerEnabled**: `true` or `false`. A missing key means `false`. Any other value, such as the string `'true'` read from an environment variable, is rejected, so that logging is never switched off by accident.
- **logger**: the name of a container service that implements `Psr\Log\LoggerInterface`. Required when **loggerEnabled** is `true`.
- **extraProvider**: optional extra request data to log; see [Extra data](extra/introduction.md).

A configuration example for the default logger, which uses `dotkernel/dot-log`, can be found in `config/log.global.php.dist`.
Any other PSR-3 logger works too: register it in your container and put its service name in **logger**.

`LogErrorHandlerFactory` checks this configuration when the container builds the error handler.
It throws `InvalidArgumentException` with a descriptive message when:

- **loggerEnabled** is not a boolean
- **loggerEnabled** is `true` but **logger** is missing or not a string
- the **logger** service does not implement `Psr\Log\LoggerInterface`
- the `Psr\Http\Message\ResponseInterface` service is not a callable response factory
- a registered `Mezzio\Middleware\ErrorResponseGenerator` service is not callable

When configuring the error handler in your application, you can choose between two classes:

- `Dot\ErrorHandler\LogErrorHandler`: for logging and displaying errors
- `Dot\ErrorHandler\ErrorHandler`: for displaying errors only

> If logging fails, for example because the log file cannot be written or a custom processor throws, the error response is still returned.
> The original error is then written to PHP's `error_log()`, followed by "(logging failed: ...)".

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
