## Executor Paths

Executors live in the `executors` folder of your project. Create one somewhere else with `--path`, relative to the project root:

```bash title="Terminal" no-line-numbers
php artisan make:executor ImportCountries --path=modules/geo/executors
```

Then register the folder in a service provider, so `execute`, `execute:rollback` and `execute:status` read it too:

```php title="app/Providers/AppServiceProvider.php"
use Pharaonic\Laravel\Executor\Facades\Executor;

public function boot(): void
{
    Executor::getPool()->addPath(base_path('modules/geo/executors'));
}
```

The package reads the `.php` files directly inside each folder, not in subfolders.

:::warning Unique Names
Executor names must be unique across all folders. Two files with the same name throw `LogicException`.
:::
