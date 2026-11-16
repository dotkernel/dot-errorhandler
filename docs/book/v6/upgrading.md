# Upgrading

## From 5.x to 6.0

The table lists every change in 6.0 that can affect an application, what you would notice, and what to do.

| Change | Symptom | Fix |
| --- | --- | --- |
| PHP 8.2 support dropped | Composer refuses to install | Use PHP 8.3 or later |
| `ext-mbstring` is required | Composer refuses to install | Enable the `mbstring` extension |
| `dotkernel/dot-log` is no longer installed with this package | `Class "Dot\Log\Logger" not found`, or the `dot-log.default_logger` service is missing | `composer require dotkernel/dot-log` |
| `laminas/laminas-diactoros` and `mezzio/mezzio` are no longer installed with this package | Missing PSR-7 or Mezzio classes, in applications that relied on this package to install them | Require them directly |
| Providers are enabled only by `'enabled' => true` | A provider configured with `1`, `'yes'` or `'true'` stops logging | Use `true` |
| `loggerEnabled` must be a boolean; a missing key means `false` | `InvalidArgumentException: Logger: '[loggerEnabled'] must be a boolean` | Use `true` or `false`, and cast values read from environment variables |
| The logger service must implement `Psr\Log\LoggerInterface` | `InvalidArgumentException` when the container builds the error handler | Point `logger` at a PSR-3 service |
| A non-callable `ResponseInterface` or `ErrorResponseGenerator` service is rejected | `InvalidArgumentException` instead of a `TypeError` | Register a callable |
| `config/error-handling.global.php.dist` masks `authorization` and `proxy-authorization` headers | Nothing: a config file copied from an earlier version keeps the old behaviour | Add both names to the `HeaderProvider` `sensitiveParameters` in your copy |
| Masking counts characters, not bytes | Masked non-ASCII values have a different length in new log lines | None |
| `LogErrorHandlerFactory::__invoke()` returns `LogErrorHandler` | Fatal error in a subclass that overrides it with return type `MiddlewareInterface` | Change the override's return type to `LogErrorHandler` |
| `LogErrorHandler` extends `ErrorHandler` | `instanceof ErrorHandler` is now also true for `LogErrorHandler` | Check for `LogErrorHandler` first |
| A logger or extra-data processor that throws no longer breaks the error response | The original error appears in PHP's `error_log` with "(logging failed: ...)" | Fix the logger or processor; nothing to change in the application |

### Example: requiring the logger

If your configuration uses `dot-log.default_logger`, as `config/log.global.php.dist` does, require dot-log next to this package:

```shell
composer require dotkernel/dot-errorhandler:^6.0 dotkernel/dot-log
```

### Example: masking credentials in headers

Before, in a copy of the 5.x config file:

```php
HeaderProvider::class => [
    'enabled'   => true,
    'processor' => [
        'class'               => HeaderProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
    ],
],
```

After:

```php
HeaderProvider::class => [
    'enabled'   => true,
    'processor' => [
        'class'               => HeaderProcessor::class,
        'replacementStrategy' => ReplacementStrategy::Full,
        'sensitiveParameters' => [
            'authorization',
            'proxy-authorization',
        ],
    ],
],
```

## From 4.x to 6.0

Start with the steps for your release, then apply every row of the [5.x to 6.0](#from-5x-to-60) table.

- **4.2 to 4.4 (branch 4.1, PSR-Log):** 5.0 changed only the minimum versions of its dependencies (`dotkernel/dot-log ^5.3`, `mezzio/mezzio ^3.28.1`, `laminas/laminas-stratigility ^4.3.1`); in the code, only two comments changed. No extra steps.
- **4.0 to 4.1 (branches 4.0 and 4.1, Laminas Log):** these releases require `dotkernel/dot-log` 4, which is based on Laminas Log. Version 6 needs a PSR-3 logger. Move your logger configuration to `dotkernel/dot-log` 5 or later, following the [dot-log documentation](https://docs.dotkernel.org/dot-log/), or register any other PSR-3 logger.
