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

`$servers` lists the IP addresses of the servers the executor belongs to. Leave it empty to run on every server.

```php title="executors/2026_01_15_120000_warm_caches.php"
public $servers = ['10.0.0.11', '10.0.0.12'];
```

The package compares the list with the addresses of the server's network interfaces, ignoring `127.0.0.1` and `::1`. On the other servers, the executor is skipped and doesn't appear as run.

:::info Once Means Once for All Servers
Every server shares the executors table, so a `ONCE` executor runs on only one of its servers: the first one that deploys. Use `ALWAYS` for work every listed server must do itself, such as clearing local caches, and `ONCE` for work that one server does for all of them, such as dispatching a job.
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
