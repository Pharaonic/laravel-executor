## Running Executors

`php artisan execute` reads every executor file, picks the ones that should run, and runs them in file-name order.

```bash title="Terminal" no-line-numbers
php artisan execute
```

An executor runs when it has never run before, or when its type is `ALWAYS`. Executors limited to other servers are skipped.

### Batches

Every executor that runs for the first time in the same `php artisan execute` call gets the same batch number, one higher than the highest batch in the table. An `ALWAYS` executor keeps the batch of its first run; the package counts its runs and stores the time of the last one.

### Several Servers at Once

When servers deploy at the same time, `php artisan execute` takes a cache lock, so they run the executors one after another. The next server reads the executors table only after the lock is free, so a `ONCE` executor that another server just ran is skipped.

If the lock isn't free within `pharaonic.executor.lock.seconds` (600 by default), the command fails with exit code `1`:

```text no-copy no-line-numbers
Another server has been running the executors for more than 600 seconds.
```

:::warning Shared Cache Store
The lock only works across servers when they share its cache store, such as `redis`, `database`, `memcached` or `dynamodb`. With the `file` or `array` store, each server has its own lock. Set the store with `EXECUTOR_LOCK_STORE`.
:::

### Running One Executor

Pass the executor name, which is its file name without `.php`:

```bash title="Terminal" no-line-numbers
php artisan execute 2026_01_15_120000_warm_caches
```

### Filtering by Tags

`--tags` takes a comma-separated list and runs the executors that have at least one of the tags:

```bash title="Terminal" no-line-numbers
php artisan execute --tags=cache,search
```

### Starting Over

`execute:fresh` empties the executors table and runs `php artisan execute`, so every executor runs again:

```bash title="Terminal" no-line-numbers
php artisan execute:fresh
```

:::danger Production
`execute:fresh` runs every `ONCE` executor again. Use it in local and testing environments.
:::
