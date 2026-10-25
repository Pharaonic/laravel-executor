<p align="center"><a href="https://pharaonic.dev/packages/laravel/executor" target="_blank"><img src="https://raw.githubusercontent.com/Pharaonic/laravel-executor/develop/docs/cover.webp"></a></p>

<p align="center">
  <a href="https://php.net" target="_blank"><img src="https://img.shields.io/static/v1?label=PHP&message=8.2%20|%208.3%20|%208.4&color=blue&style=flat-square" alt="PHP Version : 8.2 | 8.3 | 8.4"></a>
  <a href="https://laravel.com" target="_blank"><img src="https://img.shields.io/static/v1?label=Laravel&message=11.x&color=F05340&style=flat-square" alt="Laravel Version : 11.x"></a>
  <img src="https://img.shields.io/static/v1?label=License&message=MIT&color=brightgreen&style=flat-square" alt="License">
  <img src="https://github.com/Pharaonic/laravel-executor/actions/workflows/build.yml/badge.svg?branch=11.x" alt="Build">
  <br>
  <a href="https://packagist.org/packages/pharaonic/laravel-executor" target="_blank"><img src="https://poser.pugx.org/pharaonic/laravel-executor/v" alt="Packagist Version"></a>
  <a href="https://packagist.org/packages/pharaonic/laravel-executor" target="_blank"><img src="https://poser.pugx.org/pharaonic/laravel-executor/downloads" alt="Packagist Downloads"></a>
</p>

# Laravel Executor

Run one-time and recurring deployment tasks on your Laravel servers: Artisan commands, jobs and custom actions, tracked in batches with rollback, tags and per-server targeting.

## Installation

```bash
composer require pharaonic/laravel-executor
php artisan migrate
```

The service provider is registered automatically. Publish the configuration when you need to change it:

```bash
php artisan vendor:publish --tag=executor-config
```

## Usage

```bash
php artisan make:executor ClearCache
```

```php
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

return new class () extends Executor {
    public $type = ExecutorType::ONCE;

    public function up(): void
    {
        $this->command('cache:clear');
    }

    public function down(): void
    {
        //
    }
};
```

```bash
php artisan execute
```

## Documentation

Full documentation: https://pharaonic.dev/packages/laravel/executor

## Upgrading

From 11.0.x, rename `ExecutorType::Always` and `ExecutorType::Once` to `ExecutorType::ALWAYS` and `ExecutorType::ONCE` in every executor file. From 1.x, also run `php artisan migrate` to convert the executors table and move each executor to the new base class. The [upgrade guide](https://pharaonic.dev/packages/laravel/executor/11.x#upgrading) has every step.

## Support & Security

See [SUPPORT.md](SUPPORT.md) and [SECURITY.md](SECURITY.md).

## License

MIT. See [LICENSE](LICENSE).
