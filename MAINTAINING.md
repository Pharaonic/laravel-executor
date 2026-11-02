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

- Configuration: `pharaonic.executor.connection`, `pharaonic.executor.table`, `pharaonic.executor.server`, and `pharaonic.executor.lock.seconds`, with the `EXECUTOR_CONNECTION`, `EXECUTOR_TABLE`, `EXECUTOR_SERVER`, and `EXECUTOR_LOCK_SECONDS` environment variables. `pharaonic.executor.lock.store` (`EXECUTOR_LOCK_STORE`) was removed in the release after 10.0.3 and is ignored.
- `execute`, `execute:rollback`, and `execute:fresh` run inside the database lock: the reserved row `pharaonic:executor:lock` (server `*`) of the executors table, guarded by the unique `name` and `server` index. The executor model hides that row with a global scope. `execute` reads the records, from the write connection, only after taking the lock.
- An executor with `$servers` keeps one record per server (`server` is the first entry of `$servers` that is the server's `EXECUTOR_SERVER` name or one of its addresses; records are found by any of them); an executor without `$servers` keeps one shared record (`server` null). A shared record of an executor with `$servers` counts as run on every server.
- Commands: `make:executor`, `execute {name?} {--tags=}`, `execute:rollback {--steps=1}`, `execute:fresh`, and `execute:status`. `execute:make` and `make:execute` are hidden deprecated aliases that warn and still create the executor.
- Publish tags: `pharaonic`, `laravel-executor`, `pharaonic-config`, `executor-config`, `pharaonic-migrations`, and `executor-migrations`. The 1.x tags `config` and `migrations` stay as deprecated aliases.
- Extension points: `Classes\Executor` (with `$type`, `$tags`, `$servers`, `up()`, `down()`, and the final `job()` and `command()` helpers), the `Facades\Executor` facade, and `ExecutorPool::addPath()`.
- `ExecutorType` is a backed enum (`ALWAYS = 1`, `ONCE = 2`, stored as the value). The 1.x–12.x cases `Always` and `Once` were removed in 10.0.0. `label()` returns `Always` and `Once`, which `execute:status` shows.
- The deprecated names stay on every existing line and are removed only in the first line that supports PHP 8.6.

## Package-specific constraints

- The package requires `laravel/framework`, not single `illuminate/*` components: `job()` returns `Illuminate\Foundation\Bus\PendingDispatch` and the package relies on the foundation path helpers.
- The migration file name `2024_07_07_000001_create_executors_table.php` never changes, or applications run it again.
- `2024_07_07_000001_create_executors_table.php` creates the table and `2026_11_02_000001_add_server_to_executors_table.php` adds the `server` column; it does nothing on a table that already has it. Later schema changes go in new migrations, never in released ones, because applications don't run a migration again. 1.x created the create migration's file with another schema (`executor`, `tag`); the package doesn't convert that table, and the upgrade guide shows how to recreate it.
- `tests/Compatibility` pins the names kept from 1.x, 11.x, and 12.x.

## Branch notes

Fixes land on the oldest affected line and are ported to every later line and `develop`.

## Documentation

https://pharaonic.dev/packages/laravel/executor

Keep this file factual and package-specific. General Pharaonic engineering policy belongs in the private engineering repository, not here.
