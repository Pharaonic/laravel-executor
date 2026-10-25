## Basic Usage

Create an executor, describe the work in `up()`, and run it.

### 1. Create an Executor

```bash title="Terminal" no-line-numbers
php artisan make:executor WarmCaches
```

The command creates a timestamped file in the `executors` folder of your project, such as `executors/2026_01_15_120000_warm_caches.php`. The file name without `.php` is the executor's name.

### 2. Write the Task

```php title="executors/2026_01_15_120000_warm_caches.php"
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

return new class () extends Executor {
    public $type = ExecutorType::ONCE;

    public $tags = [];

    public $servers = [];

    public function up(): void
    {
        $this->command('optimize');
    }

    public function down(): void
    {
        $this->command('optimize:clear');
    }
};
```

### 3. Run It

```bash title="Terminal" no-line-numbers
php artisan execute
```

```text no-copy no-line-numbers
Executing 2026_01_15_120000_warm_caches...
```

Running the command again does nothing, because a `ONCE` executor has already run:

```text no-copy no-line-numbers
There are no executors need to be executed.
```

### 4. Check the Status

```bash title="Terminal" no-line-numbers
php artisan execute:status
```

```text no-copy no-line-numbers
+-----------------------------------+------+------+---------+-------+----------+
| Name                              | Type | Tags | Servers | Batch | Executed |
+-----------------------------------+------+------+---------+-------+----------+
| 2026_01_15_120000_warm_caches     | Once | None | All     | 1     | Yes      |
+-----------------------------------+------+------+---------+-------+----------+
```
