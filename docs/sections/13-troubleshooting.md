## Troubleshooting

### "There are no executors need to be executed."

Every `ONCE` executor has already run and there's no `ALWAYS` executor. Check the list with `php artisan execute:status`. If the executor lives outside the `executors` folder, register its folder with `Executor::getPool()->addPath()`.

### "Undefined constant ExecutorType::Once"

The executor uses a 1.x case name. Rename `ExecutorType::Always` to `ExecutorType::ALWAYS` and `ExecutorType::Once` to `ExecutorType::ONCE`, as described in [Upgrading](#upgrading).

### "Class Pharaonic\Laravel\Executor\Executor not found"

The executor still extends the 1.x class. Use `Pharaonic\Laravel\Executor\Classes\Executor`, rename `handle()` to `up()`, and add a `down()` method, as described in [Upgrading](#upgrading).

### "Column not found: name" after upgrading from 1.x

The executors table still has the 1.x shape. Run `php artisan migrate`, which converts it.

### "Another server has been running the executors for more than 600 seconds."

Another `php artisan execute` holds the lock: a deploy is still running, or a run stopped before releasing it and the lock hasn't expired yet. Wait for the other deploy, or raise `EXECUTOR_LOCK_SECONDS` when your executors take longer than 600 seconds.

### A `ONCE` executor ran on two servers

The servers don't share the lock's cache store. Point `EXECUTOR_LOCK_STORE` at a store every server uses, such as `redis` or `database`.

### An executor doesn't appear in `execute:status`

The file must be directly inside an executor folder, end in `.php`, and return the executor instance (`return new class () extends Executor { ... };`). Files that return anything else are skipped.

### "Duplicate Executor name"

Two folders contain a file with the same name. Rename one of them, keeping its timestamp prefix.

### "The type of the executor must be an instance of ExecutorType."

`$type` holds something other than a case, such as `1` or `'once'`. Use `ExecutorType::ONCE` or `ExecutorType::ALWAYS`.

### An executor runs on the wrong server

`$servers` is compared with the addresses of the server's network interfaces. Behind NAT or a load balancer, use the server's private address. Check what the package sees with `php artisan tinker` and `Executor::getIPs()`.

### `execute:make` prints a deprecation notice

The command was renamed. Use `php artisan make:executor`.
