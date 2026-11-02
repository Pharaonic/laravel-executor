<p align="center"><a href="https://pharaonic.dev/packages/laravel/executor" target="_blank"><img src="https://raw.githubusercontent.com/Pharaonic/laravel-executor/13.x/docs/cover.webp"></a></p>

<p align="center">
  <a href="https://php.net" target="_blank"><img src="https://img.shields.io/static/v1?label=PHP&message=8.3%20|%208.4%20|%208.5&color=blue&style=flat-square" alt="PHP Version : 8.3 | 8.4 | 8.5"></a>
  <a href="https://laravel.com" target="_blank"><img src="https://img.shields.io/static/v1?label=Laravel&message=13.x&color=F05340&style=flat-square" alt="Laravel Version : 13.x"></a>
  <img src="https://img.shields.io/static/v1?label=License&message=MIT&color=brightgreen&style=flat-square" alt="License">
  <img src="https://github.com/Pharaonic/laravel-executor/actions/workflows/build.yml/badge.svg?branch=13.x" alt="Build">
  <br>
  <a href="https://packagist.org/packages/Pharaonic/laravel-executor" target="_blank"><img src="https://img.shields.io/static/v1?label=Packagist&message=pharaonic/laravel-executor&color=blue&logo=packagist&logoColor=white" alt="Source"></a>
  <a href="https://packagist.org/packages/pharaonic/laravel-executor" target="_blank"><img src="https://poser.pugx.org/pharaonic/laravel-executor/v" alt="Packagist Version"></a>
  <a href="https://packagist.org/packages/pharaonic/laravel-executor" target="_blank"><img src="https://poser.pugx.org/pharaonic/laravel-executor/downloads" alt="Packagist Downloads"></a>
</p>

<h3 align="center">Run deployment tasks in Laravel once or on every deploy.</h3>
<h5 align="center">Artisan commands, queued jobs and data fixes, tracked in batches with rollback.</h5>
<br>

## Installation

```bash
composer require pharaonic/laravel-executor
php artisan migrate
```

The service provider is registered automatically, and the package loads its migration, so `php artisan migrate` creates the `executors` table.

## Usage

An executor is a deployment task, such as clearing caches, dispatching a job, or fixing data. Each one lives in its own file, runs once or on every deploy, and is tracked in a batch that can be rolled back.

### Create an Executor

```bash
php artisan make:executor WarmCaches
```

The command creates a timestamped file in the `executors` folder, such as `executors/2026_01_15_120000_warm_caches.php`:

```php
use App\Jobs\RecalculateRatings;
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

return new class () extends Executor {
    public $type = ExecutorType::ONCE;

    public $tags = ['cache'];

    public $servers = [];

    public function up(): void
    {
        $this->command('optimize');
        $this->job(RecalculateRatings::class, 'electronics')->onQueue('ratings');
    }

    public function down(): void
    {
        $this->command('optimize:clear');
    }
};
```

| Type | Runs |
| --- | --- |
| `ExecutorType::ONCE` | The first time `php artisan execute` sees it, then never again |
| `ExecutorType::ALWAYS` | Every time `php artisan execute` runs (the default) |

`$this->command()` calls an Artisan command and `$this->job()` dispatches a job. `down()` runs when the executor's batch is rolled back.

### Run the Executors

Add it to your deploy script:

```bash
php artisan execute
```

Executors run in file-name order, and every executor that runs for the first time in the same call shares one batch. Run one executor by name, or only the executors with one of the given tags:

```bash
php artisan execute 2026_01_15_120000_warm_caches
php artisan execute --tags=cache,search
```

### Several Servers

`$servers` limits an executor to some servers, by name or by IP address. Name each server with `EXECUTOR_SERVER`, which keeps working when its address changes, such as in Docker:

```bash
EXECUTOR_SERVER=web-1
```

```php
public $servers = ['web-1', 'web-2'];
```

An executor with `$servers` keeps one record per server, so a `ONCE` executor runs once on each listed server. Leave `$servers` empty for work one server does for all of them.

When servers deploy at the same time, `php artisan execute` takes a lock in the executors table, so they run the executors one after another and a `ONCE` executor never runs twice.

### Roll Back and Check

```bash
php artisan execute:rollback            # roll back the last batch
php artisan execute:rollback --steps=2  # roll back the last two batches
php artisan execute:status              # list every executor and whether it ran
php artisan execute:fresh               # empty the table and run everything again
```

`execute:fresh` runs every `ONCE` executor again, so keep it to local and testing environments.

## Documentation

For configuration, executor paths, the API reference, examples, troubleshooting, and upgrading from 1.x, see the [Laravel Executor Documentation](https://pharaonic.dev/packages/laravel/executor).

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md).

## Support & Security

See [SUPPORT.md](SUPPORT.md) and [SECURITY.md](SECURITY.md).

## License

MIT. See [LICENSE](LICENSE).
