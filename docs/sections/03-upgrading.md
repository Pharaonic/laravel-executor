## Upgrading from 1.x

This line replaces 1.x for Laravel 10. Executors become classes with `up()` and `down()`, and the executors table has a new shape. Plan the upgrade as a deploy of its own: update the package, recreate the table, then update every executor file.

### 1. Update the Package

Composer doesn't move from 1.x to this line on its own, so require it explicitly:

```bash title="Terminal" no-line-numbers
composer require pharaonic/laravel-executor:^10.0
```

### 2. Recreate the Table

The executors table has a new shape, and the package doesn't convert the 1.x table:

| 1.x column | Now |
| --- | --- |
| `executor` | `name` |
| `tag` (text) | `tags` (JSON array: `cache` becomes `["cache"]`) |
| | `servers` (new, empty) |
| `batch` (`1` by default) | `batch` (required, no default) |

1.x created the table with the same migration file name, `2024_07_07_000001_create_executors_table`, so `php artisan migrate` treats it as already run and keeps the 1.x table. Recreate the table yourself and copy the records into it, so executors that already ran don't run again. In `php artisan tinker`:

```php title="php artisan tinker"
$rows = DB::table('executors')->orderBy('id')->get();

Schema::drop('executors');
DB::table('migrations')->where('migration', '2024_07_07_000001_create_executors_table')->delete();
Artisan::call('migrate', ['--path' => 'vendor/pharaonic/laravel-executor/database/migrations', '--force' => true]);

$rows->each(fn ($row) => DB::table('executors')->insert([
    'type' => $row->type,
    'name' => $row->executor,
    'tags' => $row->tag === null ? null : json_encode([$row->tag]),
    'batch' => $row->batch,
    'executed' => $row->executed,
    'last_executed_at' => $row->last_executed_at,
]));
```

:::warning Back Up First
The records live only in the tinker session between dropping the table and inserting them again. Back up the executors table before you start. If you skip the copy, every `ONCE` executor runs again on the next `php artisan execute`.
:::

### 3. Update Every Executor File

| 1.x | Now |
| --- | --- |
| `use Pharaonic\Laravel\Executor\Executor;` | `use Pharaonic\Laravel\Executor\Classes\Executor;` |
| `public function handle(): void` | `public function up(): void`, plus a `down()` method |
| `public $tag = 'cache';` | `public $tags = ['cache'];` |
| `ExecutorType::Always` / `ExecutorType::Once` | `ExecutorType::ALWAYS` / `ExecutorType::ONCE` |

- ===1.x

  ```php title="executors/2024_07_22_100000_clear_cache.php"
  use Pharaonic\Laravel\Executor\Enums\ExecutorType;
  use Pharaonic\Laravel\Executor\Executor;

  return new class extends Executor
  {
      public $type = ExecutorType::Once;

      public $tag = 'cache';

      public function handle(): void
      {
          $this->command('cache:clear');
      }
  };
  ```

- ===Now

  ```php title="executors/2024_07_22_100000_clear_cache.php"
  use Pharaonic\Laravel\Executor\Classes\Executor;
  use Pharaonic\Laravel\Executor\Enums\ExecutorType;

  return new class () extends Executor {
      public $type = ExecutorType::ONCE;

      public $tags = ['cache'];

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

Keep each file name: it is the executor's name, and the migrated records point at it.

:::danger Old Executors Stop the Command
`php artisan execute` reads every executor file. A file that still extends the 1.x class, uses `handle()`, or uses `ExecutorType::Once` stops the whole command with an error, so update all of them before you deploy.
:::

### 4. Update Code That Uses the Package

| 1.x | Now |
| --- | --- |
| `Pharaonic\Laravel\Executor\Facades\ExecutorPool::addPath($path)` | `Pharaonic\Laravel\Executor\Facades\Executor::getPool()->addPath($path)` |
| `ExecutorPool::getPaths()` | `Executor::getPool()->getPaths()` |
| `php artisan execute:make` | `php artisan make:executor` (the old name still works and prints a deprecation notice) |
| `--tag=migrations` | `--tag=executor-migrations` |

`ExecutorType::ALWAYS->name` returns `ALWAYS`. Compare the case itself (`$type === ExecutorType::ONCE`), call `isOnce()`, or use `label()`, which returns `Always` or `Once`.

### What's New

- [Rolling back](#rolling-back) batches with `down()`.
- [Server targeting](#writing-executors) with `$servers`, and several tags per executor.
- A [lock](#running-executors) so servers deploying at the same time don't run a `ONCE` executor twice.
- A [configuration file](#configuration) for the connection, table and lock.
