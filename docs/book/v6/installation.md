# Installation

`dotkernel/dot-errorhandler` 6 requires PHP 8.3, 8.4, 8.5 or 8.6, and the `mbstring` extension.

Install it by executing the following Composer command:

```shell
composer require dotkernel/dot-errorhandler
```

## Other packages you may need

Version 6 depends only on PSR interfaces, Laminas Stratigility and Laminas Stdlib.
Depending on your application, you may need to require the following packages yourself:

- `dotkernel/dot-log`: needed if you use the logger configured in this package's `config/log.global.php.dist` (the `dot-log.default_logger` service)
- `laminas/laminas-diactoros`, or another PSR-7 implementation: needed to create the error response
- `mezzio/mezzio`: if your container registers `Mezzio\Middleware\ErrorResponseGenerator`, the error handlers use it automatically

Dotkernel and Mezzio application skeletons already require these packages.

```shell
composer require dotkernel/dot-log
```
