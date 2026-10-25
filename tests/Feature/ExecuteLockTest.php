<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

it('waits for another server and fails when it keeps the lock', function () {
    config(['pharaonic.executor.lock.seconds' => 1]);
    Cache::lock('pharaonic:executor:execute', 10)->acquire();

    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');

    $this->artisan('execute')
        ->expectsOutput('Another server has been running the executors for more than 1 seconds.')
        ->assertExitCode(1);

    expect(Recorder::$calls)->toBe([]);
});

it('reads the records after taking the lock', function () {
    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');

    // Another server ran the executor while this one waited for the lock.
    Executor::query()->create(['type' => 2, 'name' => 'once', 'batch' => 1, 'executed' => 1]);

    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');

    expect(Recorder::$calls)->toBe([]);
});

it('releases the lock after running', function () {
    $this->writeExecutor('always');

    $this->artisan('execute')->assertExitCode(0);

    expect(Cache::lock('pharaonic:executor:execute', 10)->get())->toBeTrue();
});

it('uses the configured cache store', function () {
    config(['cache.stores.executor-locks' => ['driver' => 'array'], 'pharaonic.executor.lock.store' => 'executor-locks']);
    Cache::lock('pharaonic:executor:execute', 10)->acquire();

    $this->writeExecutor('always');

    $this->artisan('execute')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['up:always']);
});

it('stops with a clear message when the cache store cannot take the lock', function () {
    config([
        'cache.stores.broken' => ['driver' => 'database', 'connection' => 'testing', 'table' => 'missing_cache', 'lock_table' => 'missing_cache_locks'],
        'pharaonic.executor.lock.store' => 'broken',
    ]);

    $this->writeExecutor('always');

    $this->artisan('execute')
        ->expectsOutputToContain("Couldn't take the executor lock on the [broken] cache store:")
        ->assertExitCode(1);

    expect(Recorder::$calls)->toBe([]);
});

it('lets the errors of an executor through and releases the lock', function () {
    File::ensureDirectoryExists(base_path('executors'));
    File::put(base_path('executors/failing.php'), <<<'PHP'
<?php

use Pharaonic\Laravel\Executor\Classes\Executor;

return new class () extends Executor {
    public function up(): void
    {
        throw new RuntimeException('The executor failed.');
    }

    public function down(): void {}
};
PHP);

    expect(fn () => $this->artisan('execute')->run())->toThrow(RuntimeException::class, 'The executor failed.')
        ->and(Cache::lock('pharaonic:executor:execute', 10)->get())->toBeTrue();
});
