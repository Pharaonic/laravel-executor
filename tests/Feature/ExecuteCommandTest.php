<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Classes\ExecutorPool;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

it('warns when there is nothing to execute', function () {
    $this->artisan('execute')
        ->expectsOutput('There are no executors need to be executed.')
        ->assertExitCode(0);
});

it('runs new executors in one batch', function () {
    $this->writeExecutor('2026_01_01_000000_first');
    $this->writeExecutor('2026_01_01_000001_second');

    $this->artisan('execute')->assertExitCode(0);

    expect(Recorder::$calls)->toBe(['up:2026_01_01_000000_first', 'up:2026_01_01_000001_second'])
        ->and(Executor::query()->orderBy('name')->pluck('batch')->all())->toBe([1, 1])
        ->and(Executor::query()->orderBy('name')->pluck('executed')->all())->toBe([1, 1]);
});

it('runs a once executor only once', function () {
    $this->writeExecutor('once', 'public $type = ExecutorType::ONCE;');

    $this->artisan('execute');
    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');

    expect(Recorder::$calls)->toBe(['up:once'])
        ->and(Executor::query()->where('name', 'once')->value('executed'))->toBe(1);
});

it('runs an always executor every time', function () {
    $this->writeExecutor('always', 'public $type = ExecutorType::ALWAYS;');

    $this->artisan('execute');
    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:always', 'up:always'])
        ->and(Executor::query()->where('name', 'always')->value('executed'))->toBe(2)
        ->and(Executor::query()->where('name', 'always')->value('batch'))->toBe(1);
});

it('runs a single executor by name', function () {
    $this->writeExecutor('first');
    $this->writeExecutor('second');

    $this->artisan('execute', ['name' => 'second']);

    expect(Recorder::$calls)->toBe(['up:second']);
});

it('runs only the executors with one of the tags', function () {
    $this->writeExecutor('cache', "public \$tags = ['cache'];");
    $this->writeExecutor('search', "public \$tags = ['search', 'index'];");
    $this->writeExecutor('untagged');

    $this->artisan('execute', ['--tags' => 'index,queue']);

    expect(Recorder::$calls)->toBe(['up:search']);
});

it('reads executors from added paths', function () {
    $this->writeExecutor('extra', '', 'custom-executors');

    ExecutorFacade::getPool()->addPath(base_path('custom-executors'));

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:extra']);
});

it('rejects duplicate executor names', function () {
    $this->writeExecutor('duplicate');
    $this->writeExecutor('duplicate', '', 'custom-executors');

    (new ExecutorPool)->addPath(base_path('custom-executors'))->collect(Executor::all());
})->throws(LogicException::class);
