## Status

`execute:status` lists every executor file with its record:

```bash title="Terminal" no-line-numbers
php artisan execute:status
```

```text no-copy no-line-numbers
+-----------------------------------+--------+---------------+-----------+-------+----------+
| Name                              | Type   | Tags          | Servers   | Batch | Executed |
+-----------------------------------+--------+---------------+-----------+-------+----------+
| 2026_01_15_120000_warm_caches     | Once   | cache, search | 10.0.0.11 | 1     | Yes      |
| 2026_01_16_090000_sync_currencies | Always | None          | All       | N/A   | No       |
+-----------------------------------+--------+---------------+-----------+-------+----------+
```

| Column | Shows |
| --- | --- |
| Name | The file name without `.php` |
| Type | `Once` or `Always` |
| Tags | The executor's tags, or `None` |
| Servers | The server IP addresses, or `All` |
| Batch | The batch of the first run, or `N/A` when it never ran |
| Executed | Whether it has run at least once |

The same data is available in code through the facade:

```php
use Pharaonic\Laravel\Executor\Facades\Executor;

Executor::info();
// ['2026_01_15_120000_warm_caches' => ['name' => ..., 'path' => ..., 'type' => ExecutorType::ONCE, 'tags' => ['cache', 'search'], 'servers' => ['10.0.0.11'], 'batch' => 1, 'executed' => true], ...]
```
