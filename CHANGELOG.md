# Changelog

All notable changes to this project are documented here.

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
