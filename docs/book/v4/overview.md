# Overview

`dot-errorhandler` is Dotkernel's logging error handler, providing two options:

- `Dot\ErrorHandler\ErrorHandler`, same as the Zend Expressive error handling class with the only difference being the removal of the `final` statement for making extension possible
- `Dot\ErrorHandler\LogErrorHandler` adds logging support to the default `ErrorHandler` class

> Versions **>=4.0.0** and **<4.1.0** use laminas/laminas-servicemanager **3.x**

> Versions **>=4.1.0** use laminas/laminas-servicemanager **4.x**
