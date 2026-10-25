<?php

declare(strict_types=1);

use Pharaonic\Laravel\Executor\Enums\ExecutorType;
use Pharaonic\Laravel\Executor\Models\Executor;

it('stores the type as its value and reads it as ExecutorType', function () {
    $model = Executor::query()->create(['type' => ExecutorType::ONCE, 'name' => 'once', 'executed' => 0]);

    expect($model->getAttributes()['type'])->toBe(2)
        ->and($model->fresh()?->type)->toBe(ExecutorType::ONCE);
});

it('runs a once executor only while it is new', function () {
    $model = new Executor(['type' => ExecutorType::ONCE, 'executed' => 0]);

    expect($model->isNew())->toBeTrue()
        ->and($model->isExecutable())->toBeTrue();

    $model->executed = 1;

    expect($model->isNew())->toBeFalse()
        ->and($model->isExecutable())->toBeFalse();
});

it('runs an always executor every time', function () {
    expect((new Executor(['type' => ExecutorType::ALWAYS, 'executed' => 5]))->isExecutable())->toBeTrue();
});

it('counts a run', function () {
    $model = Executor::query()->create(['type' => ExecutorType::ALWAYS, 'name' => 'always', 'executed' => 1]);

    $model->execute();

    expect($model->fresh()?->executed)->toBe(2)
        ->and($model->fresh()?->last_executed_at)->not->toBeNull();
});

it('reads the connection and table from the config', function () {
    config(['pharaonic.executor.table' => 'deploy_tasks', 'pharaonic.executor.connection' => 'other']);

    $model = new Executor;

    expect($model->getTable())->toBe('deploy_tasks')
        ->and($model->getConnectionName())->toBe('other');
});
