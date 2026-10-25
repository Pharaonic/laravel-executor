## Configuration

The package works without any configuration. Publish the file when you need another connection or table:

```bash title="Terminal" no-line-numbers
php artisan vendor:publish --tag=executor-config
```

```php title="config/pharaonic/executor.php"
return [
    /**
     * The database connection that should be used by the executor.
     */
    'connection' => env('EXECUTOR_CONNECTION', config('database.default')),

    /**
     * The table that should be used to store the executors.
     */
    'table' => env('EXECUTOR_TABLE', 'executors'),

    /**
     * The lock that makes servers deploying at the same time run the
     * executors one after another. Use a cache store every server shares
     * (redis, database, memcached or dynamodb); null uses the default store.
     * `seconds` is how long a run may hold the lock and how long the next
     * server waits for it.
     */
    'lock' => [
        'store' => env('EXECUTOR_LOCK_STORE'),
        'seconds' => env('EXECUTOR_LOCK_SECONDS', 600),
    ],
];
```

| Key | Environment variable | Default | Description |
| --- | --- | --- | --- |
| `pharaonic.executor.connection` | `EXECUTOR_CONNECTION` | your default connection | Database connection of the executors table |
| `pharaonic.executor.table` | `EXECUTOR_TABLE` | `executors` | Name of the executors table |
| `pharaonic.executor.lock.store` | `EXECUTOR_LOCK_STORE` | your default cache store | Cache store of the lock around `php artisan execute` |
| `pharaonic.executor.lock.seconds` | `EXECUTOR_LOCK_SECONDS` | `600` | How long a run may hold the lock, and how long another server waits for it |

:::info Same Database on Every Server
All your servers should share the same executors table. The table is how a `ONCE` executor knows it already ran.
:::
