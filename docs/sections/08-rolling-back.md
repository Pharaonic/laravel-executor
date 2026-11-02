## Rolling Back

`execute:rollback` reverses the latest batch: it calls `down()` on each executor of the batch and deletes their records, so they run again on the next `php artisan execute`.

```bash title="Terminal" no-line-numbers
php artisan execute:rollback
```

Roll back several batches with `--steps`:

```bash title="Terminal" no-line-numbers
php artisan execute:rollback --steps=3
```

:::info Rollbacks per Server
A rollback only touches the records this server sees: the records of executors without `$servers`, and this server's own records of executors with `$servers`. It never runs `down()` for an executor limited to other servers, and it leaves their records for them. To roll back an executor on each of its servers, run `execute:rollback` on each of them. `--steps` counts the batches of this server.
:::

:::info Deleted Files
The records of the rolled-back batches are deleted even when an executor file no longer exists; `down()` only runs for the files that do.
:::
