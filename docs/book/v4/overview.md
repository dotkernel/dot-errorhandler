# Overview

`dot-errorhandler` is Dotkernel's logging error handler, providing two options:

## Version History

| Branch | Release  | PSR-11 | Log Style Implementation | OSS Lifecycle                                                                                                                                     | PHP Version                                                                                                      |
|--------|----------|--------|--------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------|
| 5.0    | `>= 5.0` | 2      | PSR-Log                  | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F5.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/5.0.0) |
| 4.1    | `>= 4.2` | 2      | PSR-Log                  | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.4.1) |
| 4.1    | `< 4.2`  | 2      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.1%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.1.1) |
| 4.0    | `< 4.1`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F4.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.0.2) |
| 3.0    | `< 4.0`  | 1      | Laminas Log              | ![OSS Lifecycle](https://img.shields.io/osslifecycle?file_url=https%3A%2F%2Fgithub.com%2Fdotkernel%2Fdot-errorhandler%2Fblob%2F3.0%2FOSSMETADATA) | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/3.4.1) |

## Badges

![OSS Lifecycle](https://img.shields.io/osslifecycle/dotkernel/dot-errorhandler)
![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.4.0)

[![GitHub issues](https://img.shields.io/github/issues/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/issues)
[![GitHub forks](https://img.shields.io/github/forks/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/network)
[![GitHub stars](https://img.shields.io/github/stars/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/stargazers)
[![GitHub license](https://img.shields.io/github/license/dotkernel/dot-errorhandler)](https://github.com/dotkernel/dot-errorhandler/blob/4.1/LICENSE)

[![Build Static](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml/badge.svg?branch=4.1)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/continuous-integration.yml)
[![codecov](https://codecov.io/gh/dotkernel/dot-errorhandler/branch/4.1/graph/badge.svg?token=0KIJARS5RS)](https://codecov.io/gh/dotkernel/dot-errorhandler)
[![PHPStan](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml/badge.svg?branch=4.1)](https://github.com/dotkernel/dot-errorhandler/actions/workflows/static-analysis.yml)

## Features

This package provides two features:

- `Dot\ErrorHandler\ErrorHandler`, same as the Mezzio error handling class with the only difference being the removal of the `final` statement for making extension possible
- `Dot\ErrorHandler\LogErrorHandler` adds logging support to the default `ErrorHandler` class

> Versions **>=4.0.0** and **<4.1.0** use laminas/laminas-servicemanager **3.x**

> Versions **>=4.1.0** use laminas/laminas-servicemanager **4.x**
