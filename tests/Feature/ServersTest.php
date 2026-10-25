<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Classes\ExecutorManager;
use Pharaonic\Laravel\Executor\Facades\Executor as ExecutorFacade;
use Pharaonic\Laravel\Executor\Models\Executor;
use Pharaonic\Laravel\Executor\Tests\Fixtures\Recorder;

beforeEach(function () {
    ExecutorFacade::swap(new class extends ExecutorManager
    {
        public function getIPs()
        {
            return ['10.0.0.1'];
        }
    });
});

it('runs an executor on one of its servers', function () {
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1', '10.0.0.2'];");

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:web']);
});

it('never runs an executor on another server', function () {
    $this->writeExecutor('worker', "public \$servers = ['10.0.0.9'];");

    $this->artisan('execute')->expectsOutput('There are no executors need to be executed.');

    expect(Recorder::$calls)->toBe([])
        ->and(Executor::query()->count())->toBe(0);
});

it('skips an always executor that ran elsewhere on another server', function () {
    $this->writeExecutor('worker', "public \$type = ExecutorType::ALWAYS;\n    public \$servers = ['10.0.0.9'];");
    Executor::query()->create(['type' => 1, 'name' => 'worker', 'batch' => 1, 'executed' => 1]);

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe([]);
});

it('runs an executor without servers everywhere', function () {
    $this->writeExecutor('everywhere');

    $this->artisan('execute');

    expect(Recorder::$calls)->toBe(['up:everywhere']);
});

it('stores the servers of the executor', function () {
    $this->writeExecutor('web', "public \$servers = ['10.0.0.1'];");
    $this->writeExecutor('everywhere');

    $this->artisan('execute');

    expect(Executor::query()->where('name', 'web')->firstOrFail()->servers)->toBe(['10.0.0.1'])
        ->and(Executor::query()->where('name', 'everywhere')->firstOrFail()->servers)->toBeNull();
});
