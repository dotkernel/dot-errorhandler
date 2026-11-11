# Changelog

## 6.0.0 - TBD

### Changed

- Requires PHP 8.3, 8.4, 8.5 or 8.6
- Depends on `psr/log`, `psr/container` and `psr/http-server-handler` instead of `dotkernel/dot-log`, `laminas/laminas-diactoros` and `mezzio/mezzio`, which are now suggested
- `laminas/laminas-stratigility` requirement narrowed to `^4.3.1`
- `LogErrorHandler` extends `ErrorHandler`
- `LogErrorHandlerFactory::__invoke()` returns `LogErrorHandler`
- Extra providers are enabled only by `'enabled' => true`
- `loggerEnabled` must be a boolean; a missing key means `false`
- Factories validate the logger, response factory and error response generator services and throw `InvalidArgumentException`
- Any callable registered as `Mezzio\Middleware\ErrorResponseGenerator` is accepted
- Masking counts characters instead of bytes
- Logging failures are written to PHP's `error_log()` instead of breaking the error response
- `config/error-handling.global.php.dist` masks `authorization` and `proxy-authorization` headers
- PHPUnit 12, PHPStan level 8, StructArmed architecture check in `composer check`

### Added

- `ext-mbstring` requirement
- v6 documentation, including an upgrade guide

### Deprecated

- Nothing

### Removed

- PHP 8.2 support
- `dotkernel/dot-log`, `laminas/laminas-diactoros` and `mezzio/mezzio` as runtime requirements

### Fixed

- Error handling no longer throws when the logger or an extra processor fails
- PHP error handler is restored when error handling throws
- `RequestProcessor` handles list arrays, integer keys and non-string values
- Numeric cookie, header and server names no longer throw
- `ServerProcessor` handles array values such as `argv`
- `SessionProcessor` handles scalar session values
- `Cookie` header and `HTTP_COOKIE` values are masked regardless of case and separator spacing
- Partial masking keeps strings valid UTF-8, so the log record is no longer dropped
- Invalid extra provider configuration is ignored instead of throwing
- `LogErrorHandlerFactory` no longer warns when `loggerEnabled` is missing
- Service checks no longer depend on `zend.assertions`

## 5.0.0 - 2026-07-17

### Changed

- Requires `dotkernel/dot-log ^5.3`, `mezzio/mezzio ^3.28.1` and `laminas/laminas-stratigility ^3.0 || ^4.3.1`

### Added

- v5 documentation (#69)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.4.2 - 2025-11-13

### Changed

- README, SECURITY and documentation updates

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.4.1 - 2025-11-06

### Changed

- Allow installing with `laminas/laminas-stratigility` 3.x or 4.x (#61)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.4.0 - 2025-10-30

### Changed

- Nothing

### Added

- PHP 8.4 and 8.5 support (#56)

### Deprecated

- Nothing

### Removed

- PHP 8.1 support (#56)

### Fixed

- Nothing

## 4.3.2 - 2025-10-14

### Changed

- `log-files.md` documentation update

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.3.1 - 2025-10-14

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Zend Expressive references (#52)

### Fixed

- Nothing

## 4.3.0 - 2025-03-04

### Changed

- Psalm replaced with PHPStan (#50)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.2.1 - 2025-02-28

### Changed

- Nothing

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Server value replacement error caused by non-string values (#48)

## 4.2.0 - 2025-02-24

### Changed

- Dependency updates

### Added

- Control of the `extra` log contents through extra providers and processors (#43)

### Deprecated

- Nothing

### Removed

- Duplicate `MiddlewareInterface` from `ErrorHandler`

### Fixed

- Nothing

## 4.1.1 - 2025-01-28

### Changed

- Psalm 6.x (#41)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.1.0 - 2025-01-17

### Changed

- Nothing

### Added

- Support for `laminas/laminas-servicemanager` 4.x (#39)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.0.2 - 2025-11-06

### Changed

- Allow installing with `laminas/laminas-stratigility` 3.x or 4.x (#60)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 4.0.1 - 2025-10-30

### Changed

- Nothing

### Added

- PHP 8.4 and 8.5 support (#55)

### Deprecated

- Nothing

### Removed

- PHP 8.1 support (#55)

### Fixed

- Nothing

## 4.0.0 - 2024-08-30

### Changed

- Upgraded `dotkernel/dot-log` to 4.x (#36)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.4.1 - 2024-05-21

### Changed

- Dependency updates

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.4.0 - 2024-05-03

### Changed

- Workflow updates

### Added

- Documentation

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.3.3 - 2024-02-28

### Changed

- Updated dependency versions

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.3.2 - 2023-12-06

### Changed

- Dependency updates (#27)

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.3.1 - 2023-08-16

### Changed

- `psr/http-message ^1.0 || ^2.0` (#25)

### Added

- Codecov workflow and badge

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.3.0 - 2023-07-19

### Changed

- Code quality improvements (#13)

### Added

- GitHub Actions workflows

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.2.1 - 2022-10-28

### Changed

- Dependency updates

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.2.0 - 2022-03-22

### Changed

- Dependency and badge updates

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.1.1 - 2021-04-26

### Changed

- Nothing

### Added

- Date-based custom log file names (#3)

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.1.0 - 2020-11-02

### Changed

- Composer 2 compatibility.
- Update composer.json to require version 2 of laminas/laminas-dependency-plugin to be Composer 2 compatible.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.2 - 2020-04-23

### Changed

- Nothing

### Added

- Keywords in composer.json
- Requirement for PHP v7.4 or higher

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.1 - 2020-04-22

### Changed

- Updated 3rd party dependencies to latest version
- Updated License
- Updated .gitignore

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 3.0.0 - 2020-04-09

### Changed

- Nothing

### Added

- Missing dependency

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 2.0.0 - 2020-01-30

### Changed

- Laminas and Mezzio migration.

### Added

- Nothing

### Deprecated

- Nothing

### Removed

- Support

### Fixed

- Nothing

## 1.0.1 - 2018-11-27

Corrected factory and config provider.

### Added

- Factory for plain `ErrorHandler`

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing

## 1.0.0 - 2018-11-14

Initial release

### Added

- Everything

### Deprecated

- Nothing

### Removed

- Nothing

### Fixed

- Nothing
