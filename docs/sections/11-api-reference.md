## API Reference

### Artisan Commands

| Command | Description |
| --- | --- |
| `make:executor {name} {--path=}` | Create an executor in `executors`, or in `--path` relative to the project root |
| `execute {name?} {--tags=}` | Run the executors that should run, optionally one by name or those with one of the tags |
| `execute:rollback {--steps=1}` | Call `down()` on the executors of the latest batches and delete their records |
| `execute:fresh` | Empty the executors table and run `execute` |
| `execute:status` | List every executor with its type, tags, servers, batch and status |

### Executor

`Pharaonic\Laravel\Executor\Classes\Executor`, the class every executor extends.

| Member | Description |
| --- | --- |
| `public $type` | `ExecutorType::ONCE` or `ExecutorType::ALWAYS` (default) |
| `public $tags` | Array of tags, empty by default |
| `public $servers` | Array of server IP addresses, empty for every server |
| `up(): void` | Runs the task |
| `down(): void` | Reverses the task on rollback |
| `command(string $command, array $parameters = []): int` | Calls an Artisan command and returns its exit code |
| `job(string\|object $job, mixed ...$arguments): PendingDispatch` | Dispatches a job class with its constructor arguments, or a job instance |

### ExecutorType

`Pharaonic\Laravel\Executor\Enums\ExecutorType`, a backed enum that uses `Pharaonic\SmartEnum\SmartEnum`.

| Member | Description | Returns |
| --- | --- | --- |
| `ExecutorType::ALWAYS` | Runs on every `execute`, stored as `1` | `ExecutorType` |
| `ExecutorType::ONCE` | Runs a single time, stored as `2` | `ExecutorType` |
| `isAlways()` | Whether the type is `ALWAYS` | `bool` |
| `isOnce()` | Whether the type is `ONCE` | `bool` |
| `label()` | `Always` or `Once` | `string` |
| `options()` | Labels keyed by value: `[1 => 'Always', 2 => 'Once']` | `array` |
| `names()` / `values()` / `labels()` | Every case name, value or label | `array` |
| `fromName()` / `tryFromName()` / `hasName()` | Find a case by name (`'ONCE'`) | `ExecutorType` / `?ExecutorType` / `bool` |
| `hasValue()` | Whether a case has the value | `bool` |
| `is()` / `isNot()` / `in()` / `notIn()` / `eq()` | Compare with a case, name or value | `bool` |
| `from()` / `tryFrom()` / `cases()` | PHP's native enum methods | |

### Facade

`Pharaonic\Laravel\Executor\Facades\Executor`

| Method | Description | Returns |
| --- | --- | --- |
| `getPool()` | The pool of executor folders | `ExecutorPool` |
| `getRecords()` | Every executor record, keyed by name | `Collection` |
| `getNextBatchNumber()` | One higher than the highest batch | `int` |
| `getIPs()` | The server's IP addresses, without `127.0.0.1` and `::1` | `array` |
| `info()` | Every executor's name, path, type, tags, servers, batch and status, keyed by name | `array` |

### ExecutorPool

`Pharaonic\Laravel\Executor\Classes\ExecutorPool`, returned by `Executor::getPool()`.

| Method | Description | Returns |
| --- | --- | --- |
| `addPath(string $path)` | Adds a folder of executors | `static` |
| `getPaths()` | The executor folders | `array` |

### Model

`Pharaonic\Laravel\Executor\Models\Executor`, one record per executor that has run.

| Attribute | Type | Description |
| --- | --- | --- |
| `id` | `int` | Primary key |
| `type` | `ExecutorType` | The executor's type |
| `name` | `string` | The executor's file name without `.php` |
| `tags` | `?array` | The executor's tags |
| `servers` | `?array` | The executor's server IP addresses |
| `batch` | `?int` | The batch of the first run |
| `executed` | `int` | How many times it has run |
| `last_executed_at` | `?CarbonInterface` | When it last ran |

| Method | Description | Returns |
| --- | --- | --- |
| `isNew()` | Whether it has never run | `bool` |
| `isExecutable()` | Whether its type is `ALWAYS` or it has never run | `bool` |
| `execute()` | Counts a run and stores its time | `void` |

### Publish Tags

| Tag | Publishes |
| --- | --- |
| `executor-config`, `pharaonic-config` | `config/pharaonic/executor.php` |
| `executor-migrations`, `pharaonic-migrations` | The migration, into `database/migrations` |
| `laravel-executor`, `pharaonic` | Both |
