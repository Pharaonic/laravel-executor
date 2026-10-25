## Upgrading

This section covers upgrading to the {release.label} line from 1.x, 11.0.x or 12.0.x.

### Executor Types Are Upper Case

The `ExecutorType` cases are renamed, and the old names are removed. Executors that still use them stop with an `Undefined constant` error, so update every executor file:

| Before | Now |
| --- | --- |
| `ExecutorType::Always` | `ExecutorType::ALWAYS` |
| `ExecutorType::Once` | `ExecutorType::ONCE` |

```php title="executors/2024_07_22_100000_clear_cache.php"
// Before
public $type = ExecutorType::Once;

// Now
public $type = ExecutorType::ONCE;
```

You can update every executor in one step:

```bash title="Terminal" no-line-numbers
sed -i 's/ExecutorType::Always/ExecutorType::ALWAYS/g; s/ExecutorType::Once/ExecutorType::ONCE/g' executors/*.php
```

:::warning Case Names
`->name` now returns `ALWAYS` or `ONCE`. If your code compares the name, compare the case itself (`$type === ExecutorType::ONCE`), call `isOnce()`, or use `label()`, which still returns `Always` or `Once`.
:::

The stored values are unchanged (`1` for `ALWAYS`, `2` for `ONCE`), so the records already in your `executors` table keep working and nothing needs to be migrated.

### Command Names

`make:executor` is the command that creates an executor. The older `execute:make` and `make:execute` still work, print a deprecation notice, and are hidden from `php artisan list`.

| Deprecated | Use |
| --- | --- |
| `php artisan execute:make` | `php artisan make:executor` |
| `php artisan make:execute` | `php artisan make:executor` |

### Publish Tags

The package has its own publish tags now. The generic `config` and `migrations` tags still publish its files, but they also publish the files of every other package that uses them.

| Deprecated | Use |
| --- | --- |
| `--tag=config` | `--tag=executor-config` |
| `--tag=migrations` | `--tag=executor-migrations` |

### Exceptions

An invalid executor declaration throws `InvalidArgumentException`, and two executors with the same file name throw `LogicException`. Both extend `Exception`, so existing `catch (Exception $e)` blocks still catch them.
