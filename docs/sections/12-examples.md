## Examples

### 1. Deploy Script

Run the executors on every server after the migrations.

```bash title="deploy.sh"
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan execute
php artisan up
```

### 2. One-Time Data Fix

Fill a new column once, in chunks, and clear it again on rollback.

```php title="executors/2026_02_01_090000_fill_product_slugs.php"
use App\Models\Product;
use Illuminate\Support\Str;
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

return new class () extends Executor {
    public $type = ExecutorType::ONCE;

    public function up(): void
    {
        Product::query()->whereNull('slug')->chunkById(500, function ($products) {
            foreach ($products as $product) {
                $product->update(['slug' => Str::slug($product->name)]);
            }
        });
    }

    public function down(): void
    {
        Product::query()->update(['slug' => null]);
    }
};
```

### 3. Caches on Every Deploy

Rebuild the caches on every deploy, on every server.

```php title="executors/2026_02_01_090100_rebuild_caches.php"
use Pharaonic\Laravel\Executor\Classes\Executor;
use Pharaonic\Laravel\Executor\Enums\ExecutorType;

return new class () extends Executor {
    public $type = ExecutorType::ALWAYS;

    public $tags = ['cache'];

    public function up(): void
    {
        $this->command('optimize');
        $this->command('view:cache');
    }

    public function down(): void
    {
        $this->command('optimize:clear');
    }
};
```

Rebuild only the caches, without the other executors:

```bash title="Terminal" no-line-numbers
php artisan execute --tags=cache
```

### 4. A Task for the Worker Servers

Queue a recalculation once, from one of the servers that run the queue workers.

- ===Executor

  ```php title="executors/2026_02_01_090200_recalculate_ratings.php"
  use App\Jobs\RecalculateRatings;
  use Pharaonic\Laravel\Executor\Classes\Executor;
  use Pharaonic\Laravel\Executor\Enums\ExecutorType;

  return new class () extends Executor {
      public $type = ExecutorType::ONCE;

      public $tags = ['ratings'];

      public $servers = ['10.0.0.21', '10.0.0.22'];

      public function up(): void
      {
          $this->info('Queueing the rating recalculation...');

          $this->job(RecalculateRatings::class)->onQueue('ratings');
      }

      public function down(): void
      {
          //
      }
  };
  ```

- ===Job

  ```php title="app/Jobs/RecalculateRatings.php"
  namespace App\Jobs;

  use App\Models\Product;
  use Illuminate\Bus\Queueable;
  use Illuminate\Contracts\Queue\ShouldQueue;
  use Illuminate\Foundation\Bus\Dispatchable;
  use Illuminate\Queue\InteractsWithQueue;

  class RecalculateRatings implements ShouldQueue
  {
      use Dispatchable, InteractsWithQueue, Queueable;

      public function handle(): void
      {
          Product::query()->chunkById(500, function ($products) {
              foreach ($products as $product) {
                  $product->update(['rating' => $product->reviews()->avg('stars') ?? 0]);
              }
          });
      }
  }
  ```
