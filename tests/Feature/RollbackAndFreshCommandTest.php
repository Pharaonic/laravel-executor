<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

it('warns when nothing was executed', function () {
    $this->artisan('execute:rollback')
        ->expectsOutput('There are no executors has been found.')
        ->assertExitCode(0);
});

it('rolls back the latest batch', function () {
    $this->writeExecutor('first', 'public $type = ExecutorType::ONCE;');
    $this->artisan('execute');

    $this->writeExecutor('second', 'public $type = ExecutorType::ONCE;');
    $this->artisan('execute');

    Recorder::$calls = [];

    $this->artisan('execute:rollback')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['down:second'])
        ->and(Executor::query()->pluck('name')->all())->toBe(['first']);
});

it('rolls back several batches', function () {
    $this->writeExecutor('first', 'public $type = ExecutorType::ONCE;');
    $this->artisan('execute');

    $this->writeExecutor('second', 'public $type = ExecutorType::ONCE;');
    $this->artisan('execute');

    Recorder::$calls = [];

    $this->artisan('execute:rollback', ['--steps' => 2])->assertExitCode(0);

    expect(Recorder::$calls)->toEqualCanonicalizing(['down:first', 'down:second'])
        ->and(Executor::query()->count())->toBe(0);
});

it('forgets every run and executes again when fresh', function () {
    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');
    $this->artisan('execute');

    $this->artisan('execute:fresh')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['up:once', 'up:once'])
        ->and(Executor::query()->count())->toBe(1);
});
