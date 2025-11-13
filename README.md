# dot-errorhandler

Error Logging Handler for Dotkernel

## Version History

| Branch | Release  | PSR-11 | Log Style Implementation | OSS Lifecycle                                                                                                                                      | PHP Version                                                                                                      |
|--------|----------|--------|--------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------|
| 4.1    | `>= 4.2` | 2      | PSR-Log                  | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA)  | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.4.1) |
| 4.1    | `< 4.2`  | 2      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA)  | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.1.1) |
| 4.0    | `< 4.1`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.0%2FOSSMETADATA)  | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.0.2) |
| 3.0    | `< 4.0`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F3.0%2FOSSMETADATA)  | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/3.4.1) |

## Documentation

Documentation is available at: https://docs.dotkernel.org/dot-errorhandler/

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-errorhandler)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.0.1)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/blob/4.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-errorhandler/branch/4.0/graph/badge.svg?token=0KIJARS5RS)](https://codecov.io/gh/dotkernel/dot-errorhandler)
[![PHPStan](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml/badge.svg?branch=4.0)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml)

## Adding the error handler

- Add the Composer package.

```shell
composer require dotkernel/dot-errorhandler
```

- Add the config provider
    - in `config/config.php` add `\Dot\ErrorHandler\ConfigProvider`
    - in `config/pipeline.php` add `\Dot\ErrorHandler\ErrorHandlerInterface::class`
        - the interface is used as an alias to keep all error handling related configurations in one file

> If you need other error handlers, you should place them before DotErrorhandler in the pipeline; else it will not be able to catch errors.
> We recommend using just one error handler unless you have an error-specific handler.

- Configure the error handler as shown below.

In `config/autoload/error-handling.global.php`:

```php
<?php

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

Example:

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
