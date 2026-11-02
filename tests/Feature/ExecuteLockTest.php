<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Pharaonic\Laravel\Executor\Classes\ExecutorLock;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

function holdTheLock(DateTimeInterface $until): void
{
    DB::table('executors')->insert([
        'name' => ExecutorLock::NAME,
        'server' => '*',
        'batch' => 0,
        'executed' => 1,
        'last_executed_at' => $until,
    ]);
}

function lockRows(): int
{
    return DB::table('executors')->where('name', ExecutorLock::NAME)->count();
}

it('waits for another server and fails when it keeps the lock', function () {
    config(['pharaonic.executor.lock.seconds' => 1]);
    holdTheLock(now()->addMinutes(10));

    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');

    $this->artisan('execute')
        ->expectsOutput('Another server has been running the executors for more than 1 seconds.')
        ->assertExitCode(1);

    expect(Recorder::$calls)->toBe([])
        ->and(lockRows())->toBe(1);
});

it('takes over a lock that has expired', function () {
    holdTheLock(now()->subSecond());

    $this->writeExecutor('always');

    $this->artisan('execute')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['up:always'])
        ->and(lockRows())->toBe(0);
});

it('lets only one lock hold the row', function () {
    $first = new ExecutorLock(60);
    $second = new ExecutorLock(60);

    expect($first->acquire())->toBeTrue()
        ->and($second->acquire())->toBeFalse();

    $second->release();
    expect(lockRows())->toBe(1);

    $first->release();
    expect($second->acquire())->toBeTrue();
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

    expect(lockRows())->toBe(0);
});

it('never shows the lock row as an executor record', function () {
    holdTheLock(now()->addMinutes(10));

    expect(Executor::query()->count())->toBe(0)
        ->and(Executor::query()->max('batch'))->toBeNull();
});

it('stops with a clear message when the table cannot hold the lock', function () {
    Schema::drop('executors');

    $this->writeExecutor('always');

    $this->artisan('execute')
        ->expectsOutputToContain("Couldn't take the executor lock:")
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
        ->and(lockRows())->toBe(0);
});

it('runs rollback and fresh under the lock', function (string $command) {
    config(['pharaonic.executor.lock.seconds' => 1]);
    holdTheLock(now()->addMinutes(10));
    Executor::query()->create(['type' => 2, 'name' => 'once', 'batch' => 1, 'executed' => 1]);

    $this->artisan($command)
        ->expectsOutput('Another server has been running the executors for more than 1 seconds.')
        ->assertExitCode(1);

    expect(Executor::query()->count())->toBe(1);
})->with(['execute:rollback', 'execute:fresh']);
