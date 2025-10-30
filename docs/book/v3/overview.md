# Overview

`dot-errorhandler` is Dotkernel's logging error handler, providing two options:

## Version History

| Branch | Release  | Service Manager   | Log style implementation | PHP Version                                                                                                      |
|--------|----------|-------------------|--------------------------|------------------------------------------------------------------------------------------------------------------|
| 4.0    | < 4.1.0  | Service Manager 3 | Laminas Log style        | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/4.0.1) |
| 3.0    | < 4.0.0  | Service Manager 3 | Laminas Log              | ![PHP from Packagist (specify version)](https://img.shields.io/packagist/php-v/dotkernel/dot-errorhandler/3.4.1) |

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

## Features

This package provides two features:

- `Dot\ErrorHandler\ErrorHandler`, same as the Mezzio error handling class with the only difference being the removal of the `final` statement for making extension possible
- `Dot\ErrorHandler\LogErrorHandler` adds logging support to the default `ErrorHandler` class
