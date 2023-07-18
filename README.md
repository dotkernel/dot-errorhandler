# dot-errorhandler

Logging Error Handler for DotKernel

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-errorhandler)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/3.2.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/blob/3.0/LICENSE)

[![SymfonyInsight](https://insight.symfony.com/projects/cf1f8d89-f230-4157-bc8b-7cce20c75454/big.svg)](https://insight.symfony.com/projects/cf1f8d89-f230-4157-bc8b-7cce20c75454)
## Adding the error handler

* Add the composer package:

`composer require dotkernel/dot-errorhandler:^3.1`


* Add the config provider
  - in `config/config.php` add `\Dot\ErrorHandler\ConfigProvider`
  - in `config/pipeline.php` add `\Dot\ErrorHandler\ErrorHandlerInterface::class`
    + the interface is used as an alias to keep all error handling related configurations in one file
    + **IMPORTANT NOTE** there should be no other error handlers after this one (only before) because the other error handler will catch the error causing dot-errorhandler not to catch any error, we recommend using just one error handler unless you have an error-specific handler
    
* Configure the error handler as shown below

configs/autoload/error-handling.global.php
```php
<?php

use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;
use Dot\ErrorHandler\ErrorHandler;

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
```

When declaring the `ErrorHandlerInterface` alias you can choose whether to log or not: 
* for logging use `LogErrorHandler`
* for the simple zend expressive handler user `ErrorHandler`

The class `Dot\ErrorHandler\ErrorHandler` is the same as the Zend Expressive error handling class
the only difference being the removal of the `final` statement for making extension possible.


The class `Dot\ErrorHandler\LogErrorHandler` is `Dot\ErrorHandler\ErrorHandler` with 
added logging support.


As a note: both `LogErrorHandler` and `ErrorHandler` have factories declare in the
package's ConfigProvider. If you need a custom ErrorHandler it must have a factory
declared in the config, as in the example.

Example:

```php
<?php

use Dot\ErrorHandler\ErrorHandlerInterface;
use Custom\MyErrorHandler;
use Custom\MyErrorHandlerFactory;


return [
    'dependencies' => [
        'factories' => [
            MyErrorHandler::class => MyCustomHandlerFactory::class,
        ],
        
        'aliases' => [
            ErrorHandlerInterface::class => MyErrorHandler::class,
        ]

    ],
    'dot-errorhandler' => [
        'loggerEnabled' => true,
        'logger' => 'dot-log.default_logger'
    ]
];
```

Config examples can be found in this project's `config` directory.


