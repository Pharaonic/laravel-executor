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
     * The name of this server, matched against the executors' `$servers`
     * next to the server's IP addresses. Give each server a fixed name when
     * its addresses change, such as in containers.
     */
    'server' => env('EXECUTOR_SERVER'),

    /**
     * The lock that makes servers deploying at the same time run the
     * executors one after another. It is a row of the executors table, so
     * every server that shares the table shares the lock. `seconds` is how
     * long a run may hold the lock and how long the next server waits for it.
     */
    'lock' => [
        'seconds' => env('EXECUTOR_LOCK_SECONDS', 600),
    ],
];
```

| Key | Environment variable | Default | Description |
| --- | --- | --- | --- |
| `pharaonic.executor.connection` | `EXECUTOR_CONNECTION` | your default connection | Database connection of the executors table |
| `pharaonic.executor.table` | `EXECUTOR_TABLE` | `executors` | Name of the executors table |
| `pharaonic.executor.server` | `EXECUTOR_SERVER` | none | Name of this server for [`$servers`](#writing-executors) |
| `pharaonic.executor.lock.seconds` | `EXECUTOR_LOCK_SECONDS` | `600` | How long a run may hold the lock, and how long another server waits for it |

:::info Same Database on Every Server
All your servers should share the same executors table. The table is how a `ONCE` executor knows it already ran, and it holds the lock. When each server has its own application database, point `EXECUTOR_CONNECTION` at a connection every server shares.
:::
