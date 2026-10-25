# Changelog

All notable changes to this project are documented here.

## 13.0.0 - 2026-10-25

First release of the Laravel 13 line, built from 12.1.0.

### Compatibility

- Laravel 13 on PHP 8.3, 8.4 or 8.5. PHP 8.2 is no longer supported on this line.
- Requires `pharaonic/php-dot-array` `~8.3.1`, `~8.4.2` or `~8.5.2`, and `pharaonic/php-smart-enum` `~8.3.0`, `~8.4.0` or `~8.5.1`, matching your PHP version.
- The same API and behavior as 12.1.0. Coming from 12.0.x or 1.x, read the 12.1.0 section below and the upgrade guide.

## 12.1.0 - 2026-10-25

First release of the new Laravel 12 line, built from 11.1.0.

### Compatibility

- Laravel 12 on PHP 8.2, 8.3, 8.4 or 8.5.
- Requires `pharaonic/php-dot-array` `~8.2.1`, `~8.3.1`, `~8.4.2` or `~8.5.2`, and `pharaonic/php-smart-enum` `~8.2.0`, `~8.3.0`, `~8.4.0` or `~8.5.1`, matching your PHP version.
- The same API and behavior as 11.1.0.

### Behavior Changes from 12.0.x

This release brings every 10.x and 11.x change up to 11.1.0 to the Laravel 12 line. Coming from 12.0.x, check these before you upgrade:

- `ExecutorType::Always` and `ExecutorType::Once` are removed. Rename them to `ExecutorType::ALWAYS` and `ExecutorType::ONCE` in every executor file; until then, `php artisan execute` fails with `Undefined constant`. `ExecutorType::ALWAYS->name` is `ALWAYS`.
- An executor with `$servers` no longer runs on other servers, including on its first run.
- `php artisan execute` takes a cache lock. Servers that deploy at the same time need a shared cache store for it, set with `EXECUTOR_LOCK_STORE`. When that store fails, the command stops before running any executor.
- Invalid executor declarations throw `InvalidArgumentException`, and duplicate executor names throw `LogicException`, instead of `Exception`.
- `make:executor` creates executors; `execute:make` and `make:execute` still work and print a deprecation notice.

Upgrading from 1.x instead? Run `php artisan migrate`, which converts the 1.x executors table, and follow the upgrade guide.

The sections below list every change.

## 11.1.0 - 2026-10-25

First release of the new Laravel 11 line, built from 10.0.2.

### Compatibility

- Laravel 11 on PHP 8.2, 8.3 or 8.4. PHP 8.1 is no longer supported on this line.
- Requires `pharaonic/php-dot-array` `~8.2.1`, `~8.3.1` or `~8.4.2`, and `pharaonic/php-smart-enum` `~8.2.0`, `~8.3.0` or `~8.4.0`, matching your PHP version.
- The same API and behavior as 10.0.2.

### Fixed

- When the cache store of the lock fails, for example a `database` store without its tables, `php artisan execute` stops before running any executor with `Couldn't take the executor lock on the [store] cache store: …` and exit code `1`, instead of a raw store error (10.0.2).

### Behavior Changes from 11.0.x

This release brings every 10.x change up to 10.0.2 to the Laravel 11 line. Coming from 11.0.0, check these before you upgrade:

- `ExecutorType::Always` and `ExecutorType::Once` are removed. Rename them to `ExecutorType::ALWAYS` and `ExecutorType::ONCE` in every executor file; until then, `php artisan execute` fails with `Undefined constant`. `ExecutorType::ALWAYS->name` is `ALWAYS`.
- An executor with `$servers` no longer runs on other servers, including on its first run.
- `php artisan execute` takes a cache lock. Servers that deploy at the same time need a shared cache store for it, set with `EXECUTOR_LOCK_STORE`.
- Invalid executor declarations throw `InvalidArgumentException`, and duplicate executor names throw `LogicException`, instead of `Exception`.
- `make:executor` creates executors; `execute:make` and `make:execute` still work and print a deprecation notice.

Upgrading from 1.x instead? Run `php artisan migrate`, which converts the 1.x executors table, and follow the upgrade guide.

The 10.0.1 and 10.0.0 sections below list every change.

## 10.0.1 - 2026-10-25

### Fixed

- Upgrading from 1.x works: a new migration converts the 1.x executors table (`executor` and `tag` columns) to the current shape (`name`, `tags` and `servers`) and keeps every record, so executors that already ran don't run again. Run `php artisan migrate` after updating. Tables created by later releases are left untouched.
- The upgrade guide covers upgrading from 1.x: the table migration, the new base class, `handle()` becoming `up()` and `down()`, `$tag` becoming `$tags`, and the `ExecutorPool` facade.

## 10.0.0 - 2026-10-25

First release of the Laravel 10 line. Every Laravel line from 10.x up carries the same package code.

### Added

- Laravel 10 line on PHP 8.1–8.3.
- `make:executor`, the canonical command to create an executor.
- `ExecutorType` helpers from `pharaonic/php-smart-enum`, such as `label()`, `options()`, `fromName()`, `is()` and `in()`.
- `executor-config`, `pharaonic-config`, `executor-migrations` and `pharaonic-migrations` publish tags.
- `php artisan execute` takes a cache lock, so servers that deploy at the same time run the executors one after another and a `ONCE` executor runs only once. Configure it with `pharaonic.executor.lock.store` (`EXECUTOR_LOCK_STORE`) and `pharaonic.executor.lock.seconds` (`EXECUTOR_LOCK_SECONDS`, 600 by default).
- Unit, feature and compatibility test suites, PHPStan and Laravel Pint.
- GitHub community files, Dependabot and a lowest/highest CI matrix.

### Changed

- `ExecutorType` cases are named `ALWAYS` and `ONCE`, so `ExecutorType::ALWAYS->name` is `ALWAYS`. `execute:status` shows the type label, which is still `Always` or `Once`.
- Invalid executor declarations throw `InvalidArgumentException`, and duplicate executor names throw `LogicException`. Both extend `Exception`, which was thrown before.
- Migrations are loaded in `boot()`. The migration creates the same table as before.
- `php artisan about` shows the installed package version.

### Fixed

- An executor with `$servers` no longer runs on other servers the first time `php artisan execute` sees it. The server list is now checked before the executor's first run, not only after it.
- The executor's `$servers` are stored in the `servers` column, which stayed empty before.

### Deprecated

Each deprecated name still works.

| Deprecated | Use |
|---|---|
| `execute:make` | `make:executor` |
| `make:execute` | `make:executor` |
| `config` publish tag | `executor-config` |
| `migrations` publish tag | `executor-migrations` |

### Removed

- `ExecutorType::Always` and `ExecutorType::Once`. Use `ExecutorType::ALWAYS` and `ExecutorType::ONCE`; executors that still use the old names fail with `Undefined constant`. The stored values (`1` and `2`) are unchanged, so existing records keep working.
