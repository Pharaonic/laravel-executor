## Rolling Back

`execute:rollback` reverses the latest batch: it calls `down()` on each executor of the batch and deletes their records, so they run again on the next `php artisan execute`.

```bash title="Terminal" no-line-numbers
php artisan execute:rollback
```

Roll back several batches with `--steps`:

```bash title="Terminal" no-line-numbers
php artisan execute:rollback --steps=3
```

:::warning Run It on the Right Server
A rollback runs `down()` on the server where you run the command, even for executors whose `$servers` list other servers, and then deletes the records for every server. Run `execute:rollback` on a server the rolled-back executors belong to.
:::

:::info Deleted Files
The records of the rolled-back batches are deleted even when an executor file no longer exists; `down()` only runs for the files that do.
:::
