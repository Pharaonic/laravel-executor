# Maintaining This Package

This file records package-specific maintainer facts that are safe to keep in the public repository.

## Purpose

Run deployment tasks (executors) on Laravel servers: each executor is a PHP file returning an anonymous `Executor` subclass with `up()` and `down()`, run by `php artisan execute`, recorded in a database table in batches, and reversible with `execute:rollback`.

## Supported Laravel lines

One branch per Laravel major, from `10.x` to the newest major. Each line's PHP range, Laravel constraint, and tools follow the Pharaonic Laravel line table. The package starts at `10.x` because `ExecutorType` is a native enum (PHP 8.1) and no release ever supported Laravel 6–9 (1.x required Laravel 10).

## Core library

None. `pharaonic/php-smart-enum` adds the `ExecutorType` helpers, and `pharaonic/php-dot-array` reads the server IP addresses in `ExecutorManager::getIPs()`. Each line requires one `~8.N.p` of each per supported PHP minor.

## Public API invariants

Configuration keys (`pharaonic.executor.*`), publish tags, and registered Blade, Artisan, macro, and validation names are public API.

- Configuration: `pharaonic.executor.connection`, `pharaonic.executor.table`, `pharaonic.executor.lock.store`, and `pharaonic.executor.lock.seconds`, with the `EXECUTOR_CONNECTION`, `EXECUTOR_TABLE`, `EXECUTOR_LOCK_STORE`, and `EXECUTOR_LOCK_SECONDS` environment variables.
- `php artisan execute` runs inside the cache lock `pharaonic:executor:execute` and reads the executor records only after taking it.
- Commands: `make:executor`, `execute {name?} {--tags=}`, `execute:rollback {--steps=1}`, `execute:fresh`, and `execute:status`. `execute:make` and `make:execute` are hidden deprecated aliases that warn and still create the executor.
- Publish tags: `pharaonic`, `laravel-executor`, `pharaonic-config`, `executor-config`, `pharaonic-migrations`, and `executor-migrations`. The 1.x tags `config` and `migrations` stay as deprecated aliases.
- Extension points: `Classes\Executor` (with `$type`, `$tags`, `$servers`, `up()`, `down()`, and the final `job()` and `command()` helpers), the `Facades\Executor` facade, and `ExecutorPool::addPath()`.
- `ExecutorType` is a backed enum (`ALWAYS = 1`, `ONCE = 2`, stored as the value). The 1.x–12.x cases `Always` and `Once` were removed in 10.0.0. `label()` returns `Always` and `Once`, which `execute:status` shows.
- The deprecated names stay on every existing line and are removed only in the first line that supports PHP 8.6.

## Package-specific constraints

- The package requires `laravel/framework`, not single `illuminate/*` components: `job()` returns `Illuminate\Foundation\Bus\PendingDispatch` and the package relies on the foundation path helpers.
- The migration file name `2024_07_07_000001_create_executors_table.php` never changes, or applications run it again.
- 1.x created the same migration file with another schema (`executor`, `tag`). `2026_10_25_000001_upgrade_executors_table_from_1x.php` converts that table and does nothing on any other table; keep it on every line.
- `tests/Compatibility` pins the names kept from 1.x, 11.x, and 12.x.

## Branch notes

Fixes land on the oldest affected line and are ported to every later line and `develop`.

## Documentation

https://pharaonic.dev/packages/laravel/executor

Keep this file factual and package-specific. General Pharaonic engineering policy belongs in the private engineering repository, not here.
