# Overview

`dot-errorhandler` is Dotkernel's logging error handler, providing two options:

- `Dot\ErrorHandler\ErrorHandler`, same as the Zend Expressive error handling class with the only difference being the removal of the `final` statement for making extension possible
- `Dot\ErrorHandler\LogErrorHandler` adds logging support to the default `ErrorHandler` class
