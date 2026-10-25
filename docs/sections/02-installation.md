## Installation

Install the package with Composer and create its table.

### Requirements

- PHP 8.3, 8.4 or 8.5
- Laravel 13

### Composer Installation

```bash title="Terminal" no-line-numbers
composer require pharaonic/laravel-executor
```

The service provider is registered automatically.

### Run Migrations

The package loads its migration automatically, so the `executors` table is created with your other migrations:

```bash title="Terminal" no-line-numbers
php artisan migrate
```

To change the migration before it runs, publish it into `database/migrations`:

```bash title="Terminal" no-line-numbers
php artisan vendor:publish --tag=executor-migrations
```

### Publish Configuration

Publish the configuration only when you need another connection or table name:

```bash title="Terminal" no-line-numbers
php artisan vendor:publish --tag=executor-config
```

The file is published to `config/pharaonic/executor.php`.

:::success Installation Complete
You're all set! Create your first executor with `php artisan make:executor`.
:::
