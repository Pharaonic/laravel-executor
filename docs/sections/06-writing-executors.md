## Writing Executors

An executor is a PHP file that returns an instance of an anonymous class extending `Pharaonic\Laravel\Executor\Classes\Executor`. Files that don't return an executor are skipped.

### Type

`$type` decides how often the executor runs. It defaults to `ExecutorType::ALWAYS`.

| Type | Runs |
| --- | --- |
| `ExecutorType::ONCE` | The first time `php artisan execute` sees it, then never again |
| `ExecutorType::ALWAYS` | Every time `php artisan execute` runs |

```php title="executors/2026_01_15_120000_warm_caches.php"
public $type = ExecutorType::ONCE;
```

### Tags

`$tags` groups executors, so you can run only some of them with `--tags`.

```php title="executors/2026_01_15_120000_warm_caches.php"
public $tags = ['cache', 'search'];
```

### Servers

`$servers` lists the servers the executor belongs to, by name or by IP address. Leave it empty to run on every server.

```php title="executors/2026_01_15_120000_warm_caches.php"
public $servers = ['web-1', 'web-2'];
```

#### Naming Servers

Give each server a fixed name with `EXECUTOR_SERVER`, and list those names in `$servers`. Names are the recommended way: they stay the same when a server's address changes, and they read better than addresses.

```bash title=".env" no-line-numbers
EXECUTOR_SERVER=web-1
```

A server matches an entry of `$servers` when the entry is its `EXECUTOR_SERVER` name or one of the addresses of its network interfaces, ignoring `127.0.0.1` and `::1`. A list can mix both, such as `['web-1', '10.0.0.12']`. On the other servers, the executor is skipped and doesn't appear as run.

:::warning Docker and Other Containers
Inside a container, the package sees the container's own address, such as `172.17.0.5`, not the address of the host, and the address changes when the container is recreated. Host addresses in `$servers` then never match, and container addresses match only until the next recreate, after which `ONCE` executors run again. Set `EXECUTOR_SERVER` in each container's environment and list the names instead.
:::

#### Records per Server

An executor with servers keeps one record per server, stored under the first entry of the list that matches the server:

| Type | Without `$servers` | With `$servers` |
| --- | --- | --- |
| `ONCE` | Runs once, on the first server that deploys | Runs once on each listed server |
| `ALWAYS` | Runs on every server, every deploy | Runs on each listed server, every deploy |

Use `$servers` with `ONCE` for setup each server needs once, such as linking local storage, and leave `$servers` empty for work one server does for all of them, such as dispatching a job.

#### Changing the List

- **Removing a server:** it stops running the executor, and `execute:rollback` on it leaves the executor alone. Its record stays, so a `ONCE` executor doesn't run again if you add the server back.
- **Adding a server:** it runs the executor on its next deploy.
- **Emptying the list:** the servers that already ran a `ONCE` executor skip it, and the first other server that deploys runs it once for all of them.
- **A server's address changes:** a server listed by address counts as a new server and runs its `ONCE` executors again. A server listed by its `EXECUTOR_SERVER` name keeps its record.
- **Switching a server from its address to a name:** set `EXECUTOR_SERVER` while the old address is still in the list, deploy once, then replace the address with the name. The record stays under the old address and the server still finds it by that address; it keeps finding it as long as the address doesn't change.

:::info Records From Earlier Releases
A record written before per-server records has no server, so it counts as run on every server and its `ONCE` executor doesn't run again.
:::

### Running Artisan Commands

`$this->command()` calls an Artisan command, prints its output, and returns its exit code.

```php
public function up(): void
{
    $this->command('cache:clear');
    $this->command('queue:restart');
}
```

### Dispatching Jobs

`$this->job()` dispatches a job. Pass the class name and its constructor arguments, or a job instance. It returns Laravel's pending dispatch, so you can chain `onQueue()`, `delay()` and the other dispatch options.

```php
use App\Jobs\RecalculateRatings;

public function up(): void
{
    $this->job(RecalculateRatings::class, 'electronics')->onQueue('ratings');
    $this->job(new RecalculateRatings('books'))->delay(now()->addMinutes(5));
}
```

:::warning Closures
`job()` doesn't accept closures or other callables and throws `InvalidArgumentException`. Use a job class.
:::

### Writing Output

Executors use Laravel's console output helpers, such as `info()`, `line()`, `warn()`, `error()` and `table()`.

```php
public function up(): void
{
    $count = Product::query()->whereNull('slug')->count();

    $this->info("Generating {$count} slugs...");
}
```

### Reversing an Executor

`down()` runs when the executor's batch is rolled back. Leave it empty when there's nothing to undo.

### Validation

The executor checks its properties when it is created and throws `InvalidArgumentException` when:

- `$type` isn't an `ExecutorType` case;
- `$tags` isn't an array;
- `$servers` isn't an array.
