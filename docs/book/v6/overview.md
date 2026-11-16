# Overview

`dot-errorhandler` is Dotkernel's PSR-15 error handler middleware, with optional logging through any PSR-3 logger.

## Version History

| Branch | Release  | PSR-11 | Log Style Implementation | OSS Lifecycle                                                                                                                                     | PHP Version                                                                                                      |
|--------|----------|--------|--------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------|
| 6.0    | `>= 6.0` | 1, 2   | PSR-Log (any logger)     | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F6.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/6.0.0) |
| 5.0    | `>= 5.0` | 2      | PSR-Log                  | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F5.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/5.0.0) |
| 4.1    | `>= 4.2` | 2      | PSR-Log                  | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.4.1) |
| 4.1    | `< 4.2`  | 2      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.1.1) |
| 4.0    | `< 4.1`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.0.2) |
| 3.0    | `< 4.0`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F3.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/3.4.1) |

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-errorhandler)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/6.0.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/blob/6.0/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml/badge.svg?branch=6.0)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-errorhandler/branch/6.0/graph/badge.svg?token=0KIJARS5RS)](https://codecov.io/gh/dotkernel/dot-errorhandler)
[![PHPStan](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml/badge.svg?branch=6.0)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml)

## Features

This package provides two middleware classes:

- `Dot\ErrorHandler\ErrorHandler`, the same as Laminas Stratigility's `ErrorHandler` middleware, which is marked `@final`, but open for extension
- `Dot\ErrorHandler\LogErrorHandler`, which extends `ErrorHandler` and logs every error through a PSR-3 logger, optionally with [extra request data](extra/introduction.md)

Version 6 works with any PSR-11 container, any PSR-3 logger and any PSR-15 middleware pipeline.
It no longer installs `dotkernel/dot-log`, `laminas/laminas-diactoros` or `mezzio/mezzio` for you; see [Installation](installation.md).

Upgrading from an earlier version? Read [Upgrading](upgrading.md).
