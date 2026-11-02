## Troubleshooting

### "There are no executors need to be executed."

Every `ONCE` executor has already run and there's no `ALWAYS` executor. Check the list with `php artisan execute:status`. If the executor lives outside the `executors` folder, register its folder with `Executor::getPool()->addPath()`.

### "Undefined constant ExecutorType::Once"

The executor uses a case name from 1.x or 12.0.x. Rename `ExecutorType::Always` to `ExecutorType::ALWAYS` and `ExecutorType::Once` to `ExecutorType::ONCE`, as described in [Upgrading](#upgrading).

### "Class Pharaonic\Laravel\Executor\Executor not found"

The executor still extends the 1.x class. Use `Pharaonic\Laravel\Executor\Classes\Executor`, rename `handle()` to `up()`, and add a `down()` method, as described in [Upgrading](#upgrading).

### "Column not found: name" after upgrading from 1.x

The executors table still has the 1.x shape, and `php artisan migrate` doesn't change it. Recreate the table and copy its records, as described in [Upgrading](#upgrading).

### "Another server has been running the executors for more than 600 seconds."

Another `php artisan execute`, `execute:rollback` or `execute:fresh` holds the lock: a deploy is still running, or a run stopped before releasing it and the lock hasn't expired yet. Wait for the other deploy, or raise `EXECUTOR_LOCK_SECONDS` when your executors take longer than 600 seconds.

### "Couldn't take the executor lock: …"

The command couldn't write the lock row to the executors table, so it stopped before running anything. The message ends with the database error. Run `php artisan migrate`, which adds the `server` column the lock needs, and check that `EXECUTOR_CONNECTION` reaches the database.

### A `ONCE` executor ran on two servers

When the executor has `$servers`, it runs once on each listed server; leave `$servers` empty to run it once for all of them. Otherwise the servers don't share the executors table: point `EXECUTOR_CONNECTION` at a connection every server uses.

### An executor doesn't appear in `execute:status`

The file must be directly inside an executor folder, end in `.php`, and return the executor instance (`return new class () extends Executor { ... };`). Files that return anything else are skipped.

### "Duplicate Executor name"

Two folders contain a file with the same name. Rename one of them, keeping its timestamp prefix.

### "The type of the executor must be an instance of ExecutorType."

`$type` holds something other than a case, such as `1` or `'once'`. Use `ExecutorType::ONCE` or `ExecutorType::ALWAYS`.

### An executor runs on the wrong server

`$servers` is compared with the server's `EXECUTOR_SERVER` name and the addresses of its network interfaces. Behind NAT or a load balancer, use the server's private address; inside Docker or another container, the package sees the container's address, so set `EXECUTOR_SERVER` and list names instead. Check what the package sees with `php artisan tinker`, `Executor::getServer()` and `Executor::getIPs()`.

### `execute:make` prints a deprecation notice

The command was renamed. Use `php artisan make:executor`.
